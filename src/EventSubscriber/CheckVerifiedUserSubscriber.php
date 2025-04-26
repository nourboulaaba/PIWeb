<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\SecurityBundle\Security;

class CheckVerifiedUserSubscriber implements EventSubscriberInterface
{
    private Security $security;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(Security $security, UrlGeneratorInterface $urlGenerator)
    {
        $this->security = $security;
        $this->urlGenerator = $urlGenerator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 0],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $user = $this->security->getUser();
        $request = $event->getRequest();

        // Vérifier si l'utilisateur est connecté, s'il s'agit d'un utilisateur de notre application
        // et s'il n'a pas vérifié son email
        if ($user instanceof User && !$user->isVerified()) {
            // Liste des routes autorisées même sans vérification d'email
            $allowedRoutes = [
                'app_login',
                'app_logout',
                'app_signup',
                'app_verify_email',
                'app_verify_resend_email',
            ];

            // Si la route actuelle n'est pas dans la liste des routes autorisées
            if (!in_array($request->attributes->get('_route'), $allowedRoutes)) {
                // Rediriger vers la page de connexion
                $event->setResponse(new RedirectResponse($this->urlGenerator->generate('app_login')));
            }
        }
    }
}
