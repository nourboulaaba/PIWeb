<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class AppAuthAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private $entityManager;

    public function __construct(private UrlGeneratorInterface $urlGenerator, \Doctrine\ORM\EntityManagerInterface $entityManager = null)
    {
        $this->entityManager = $entityManager;
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->getPayload()->getString('email');

        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->getPayload()->getString('password')),
            [
                new CsrfTokenBadge('authenticate', $request->getPayload()->getString('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        $user = $token->getUser();

        // Sécurisation du typage
        if (!$user instanceof \App\Entity\User) {
            throw new \LogicException('L\'utilisateur doit être une instance de App\Entity\User.');
        }

        // Vérification de l'état du compte (vérification d'email)
        if (!$user->isVerified()) {
            // Utiliser directement le contrôleur pour ajouter un message flash
            $request->getSession()->set('flash_warning', '<strong>Compte non activé</strong> <br><br>Votre compte n\'est pas encore activé. <br><br>Si vous avez déjà vérifié votre email, veuillez attendre qu\'un administrateur active votre compte. <br><br>Sinon, <a href="' . $this->urlGenerator->generate('app_verify_resend_email') . '">cliquez ici pour renvoyer l\'email de vérification</a>');
            return new RedirectResponse($this->urlGenerator->generate('app_login'));
        }

        // Vérification que l'utilisateur a un rôle défini
        if (empty($user->getRole())) {
            // Définir un rôle par défaut si aucun n'est défini
            $user->setRole('EMPLOYE');
            // Persister le changement
            if ($this->entityManager) {
                $this->entityManager->persist($user);
                $this->entityManager->flush();
            }
        }

        // Redirection selon le rôle
        if (in_array('ROLE_RH', $user->getRoles())) {
            return new RedirectResponse($this->urlGenerator->generate('app_user_list'));
        } elseif (in_array('ROLE_EMPLOYE', $user->getRoles())) {
            return new RedirectResponse($this->urlGenerator->generate('app_front_index'));
        } elseif (in_array('ROLE_CANDIDAT', $user->getRoles())) {
            return new RedirectResponse($this->urlGenerator->generate('app_front_index'));
        }

        // Redirection par défaut si aucun rôle connu
        return new RedirectResponse($this->urlGenerator->generate('app_front_index'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }

    // Méthode supprimée car non utilisée
}
