<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_RH')]
class AdminController extends AbstractController
{
    #[Route('/users', name: 'app_admin_users')]
    public function listUsers(EntityManagerInterface $entityManager): Response
    {
        $users = $entityManager->getRepository(User::class)->findAll();

        return $this->render('admin/users.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/user/{id}/activate', name: 'app_admin_activate_user')]
    public function activateUser(
        User $user,
        EntityManagerInterface $entityManager,
        NotificationService $notificationService
    ): Response
    {
        // Activer l'utilisateur
        $user->setIsVerified(true);

        // S'assurer que l'utilisateur a un rôle défini
        if (empty($user->getRole())) {
            $user->setRole('EMPLOYE');
        }

        // Persister les changements
        $entityManager->persist($user);
        $entityManager->flush();

        // Notifier les utilisateurs RH qu'un utilisateur a été activé
        $notificationService->notifyRhAboutNewUser($user);

        $this->addFlash('success', '<strong>Utilisateur activé</strong> <br><br>L\'utilisateur <strong>' . $user->getEmail() . '</strong> a été activé avec succès. <br>Cet utilisateur peut maintenant se connecter à son compte.');

        return $this->redirectToRoute('app_user_list');
    }

    #[Route('/user/{id}/deactivate', name: 'app_admin_deactivate_user')]
    public function deactivateUser(User $user, EntityManagerInterface $entityManager): Response
    {
        // Désactiver l'utilisateur
        $user->setIsVerified(false);

        // Persister les changements
        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success', '<strong>Utilisateur désactivé</strong> <br><br>L\'utilisateur <strong>' . $user->getEmail() . '</strong> a été désactivé avec succès. <br>Cet utilisateur ne pourra plus se connecter à son compte jusqu\'à ce qu\'il soit réactivé.');

        return $this->redirectToRoute('app_user_list');
    }
}
