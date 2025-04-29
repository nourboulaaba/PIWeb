<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GoogleController extends AbstractController
{
    #[Route('/connect/google', name: 'connect_google')]
    public function connectAction(ClientRegistry $clientRegistry): RedirectResponse
    {
        // Rediriger vers Google pour l'authentification
        return $clientRegistry
            ->getClient('google')
            ->redirect([
                'email', 'profile' // Les scopes que vous demandez à Google
            ], []);
    }

    #[Route('/connect/google/check', name: 'connect_google_check')]
    public function connectCheckAction(): Response
    {
        // Cette méthode est gérée par l'authenticator GoogleAuthenticator
        // Elle ne sera jamais appelée directement
        throw new \LogicException('Cette méthode ne devrait pas être appelée. Vérifiez votre configuration de sécurité.');
    }
}
