<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Repository\UserRepository;

class NotificationRedirectController extends AbstractController
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Redirige vers la page d'un utilisateur avec le bon template
     *
     * @Route("/notification/redirect/user/{id}", name="app_notification_redirect_user")
     */
    public function redirectToUser(int $id): RedirectResponse
    {
        // Récupérer l'utilisateur
        $user = $this->userRepository->find($id);

        if (!$user) {
            $this->addFlash('error', 'Utilisateur non trouvé');
            return $this->redirectToRoute('app_user_index');
        }

        // Rediriger vers la page d'édition de l'utilisateur
        return $this->redirectToRoute('app_user_edit', ['id' => $id]);
    }

    /**
     * Redirige vers la page d'administration des utilisateurs
     *
     * @Route("/notification/redirect/users", name="app_notification_redirect_users")
     */
    public function redirectToUsers(): RedirectResponse
    {
        return $this->redirectToRoute('app_user_index');
    }
}
