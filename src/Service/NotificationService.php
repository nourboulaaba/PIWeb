<?php

namespace App\Service;

use App\Entity\Notification;
use App\Entity\User;
use App\Repository\NotificationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class NotificationService
{
    private EntityManagerInterface $entityManager;
    private NotificationRepository $notificationRepository;
    private UserRepository $userRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        NotificationRepository $notificationRepository,
        UserRepository $userRepository
    ) {
        $this->entityManager = $entityManager;
        $this->notificationRepository = $notificationRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Crée une notification pour tous les utilisateurs avec le rôle RH
     */
    public function notifyRhAboutNewUser(User $newUser): void
    {
        // Récupérer tous les utilisateurs avec le rôle RH
        $rhUsers = $this->userRepository->findByRole('RH');

        // Compter le nombre d'utilisateurs en attente
        $pendingCount = $this->notificationRepository->countPendingUsers();

        foreach ($rhUsers as $rhUser) {
            $notification = new Notification();
            $notification->setMessage("Nouvel utilisateur inscrit : {$newUser->getFirstName()} {$newUser->getLastName()}. {$pendingCount} utilisateur(s) en attente d'activation.");
            $notification->setType('new_user');
            $notification->setUser($rhUser);
            $notification->setRelatedUserId($newUser->getId()); // Stocker l'ID de l'utilisateur concerné
            $notification->setLink('/notification/redirect/user/' . $newUser->getId()); // Lien vers la page de l'utilisateur

            $this->entityManager->persist($notification);
        }

        $this->entityManager->flush();
    }

    /**
     * Récupère les notifications non lues pour un utilisateur
     */
    public function getUnreadNotifications(User $user): array
    {
        return $this->notificationRepository->findUnreadByUser($user);
    }

    /**
     * Récupère toutes les notifications pour un utilisateur
     */
    public function getAllNotifications(User $user, int $limit = 10): array
    {
        return $this->notificationRepository->findByUser($user, $limit);
    }

    /**
     * Marque une notification comme lue
     */
    public function markAsRead(Notification $notification): void
    {
        $notification->setIsRead(true);
        $this->entityManager->flush();
    }

    /**
     * Marque toutes les notifications d'un utilisateur comme lues
     */
    public function markAllAsRead(User $user): void
    {
        $this->notificationRepository->markAllAsRead($user);
    }

    /**
     * Compte le nombre de notifications non lues pour un utilisateur
     */
    public function countUnreadNotifications(User $user): int
    {
        return count($this->notificationRepository->findUnreadByUser($user));
    }
}
