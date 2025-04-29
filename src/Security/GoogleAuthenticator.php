<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use League\OAuth2\Client\Provider\GoogleUser;

class GoogleAuthenticator extends OAuth2Authenticator implements AuthenticationEntryPointInterface
{
    private $clientRegistry;
    private $entityManager;
    private $router;

    public function __construct(ClientRegistry $clientRegistry, EntityManagerInterface $entityManager, RouterInterface $router)
    {
        $this->clientRegistry = $clientRegistry;
        $this->entityManager = $entityManager;
        $this->router = $router;
    }

    public function supports(Request $request): ?bool
    {
        // Continue only if the current route is the Google callback route
        return $request->attributes->get('_route') === 'connect_google_check';
    }

    public function authenticate(Request $request): Passport
    {
        $client = $this->clientRegistry->getClient('google');
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function() use ($accessToken, $client) {
                /** @var GoogleUser $googleUser */
                $googleUser = $client->fetchUserFromToken($accessToken);

                $email = $googleUser->getEmail();

                // 1) Have they logged in with Google before? Easy!
                $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

                if ($existingUser) {
                    // Update user information if needed
                    $existingUser->setFirstName($googleUser->getFirstName());
                    $existingUser->setLastName($googleUser->getLastName());
                    $this->entityManager->flush();

                    return $existingUser;
                }

                // 2) User doesn't exist, create them
                $user = new User();
                $user->setEmail($email);
                $user->setFirstName($googleUser->getFirstName());
                $user->setLastName($googleUser->getLastName());

                // Generate a random password (user won't use it)
                $user->setPassword(
                    password_hash(bin2hex(random_bytes(16)), PASSWORD_BCRYPT)
                );

                // Set default role
                $user->setRole('EMPLOYE');

                // Set as not verified (inactive) - admin needs to activate
                $user->setIsVerified(false);

                // Set a default salary (required field)
                $user->setSalary(0.0);

                $this->entityManager->persist($user);
                $this->entityManager->flush();

                return $user;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $user = $token->getUser();

        // Check if user is verified (active)
        if ($user instanceof \App\Entity\User && !$user->isVerified()) {
            // Add flash message
            $this->addFlash($request, 'warning', 'Votre compte a été créé avec succès mais n\'est pas encore activé. Un administrateur doit l\'activer avant que vous puissiez vous connecter.');

            // Redirect to login page
            return new RedirectResponse($this->router->generate('app_login'));
        }

        // Store the token in the session for the main firewall as well
        $request->getSession()->set('_security_main', serialize($token));

        // Redirect based on role
        if (in_array('ROLE_RH', $user->getRoles())) {
            return new RedirectResponse($this->router->generate('app_user_list'));
        } elseif (in_array('ROLE_EMPLOYE', $user->getRoles())) {
            return new RedirectResponse($this->router->generate('app_front_index'));
        } elseif (in_array('ROLE_CANDIDAT', $user->getRoles())) {
            return new RedirectResponse($this->router->generate('app_front_index'));
        }

        // Default redirect
        return new RedirectResponse($this->router->generate('app_front_index'));
    }

    /**
     * Helper method to add flash messages
     */
    private function addFlash(Request $request, string $type, string $message): void
    {
        if ($request->hasSession()) {
            $session = $request->getSession();
            // Utiliser la méthode standard de Symfony pour les messages flash
            $session->set('flash_' . $type, $message);
        }
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new RedirectResponse(
            $this->router->generate('app_login', ['error' => $message])
        );
    }

    /**
     * Called when authentication is needed, but it's not sent.
     */
    public function start(Request $request, ?AuthenticationException $authException = null): Response
    {
        return new RedirectResponse(
            $this->router->generate('app_login')
        );
    }
}
