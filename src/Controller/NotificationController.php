<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/notifications')]
class NotificationController extends AbstractController
{
    private NotificationService $notificationService;
    private EntityManagerInterface $entityManager;

    public function __construct(NotificationService $notificationService, EntityManagerInterface $entityManager)
    {
        $this->notificationService = $notificationService;
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'app_notifications')]
    #[IsGranted('ROLE_RH')]
    public function index(): Response
    {
        $user = $this->getUser();
        $notifications = $this->notificationService->getAllNotifications($user);

        return $this->render('notification/index.html.twig', [
            'notifications' => $notifications
        ]);
    }

    #[Route('/count', name: 'app_notifications_count', methods: ['GET'])]
    public function count(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['count' => 0]);
        }

        $count = $this->notificationService->countUnreadNotifications($user);

        return new JsonResponse(['count' => $count]);
    }

    public function renderNotificationMenu(): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return new Response('');
        }

        $unreadCount = $this->notificationService->countUnreadNotifications($user);

        return $this->render('notification/_notification_counter.html.twig', [
            'unreadCount' => $unreadCount
        ]);
    }

    #[Route('/mark-as-read/{id}', name: 'app_notification_mark_as_read', methods: ['POST'])]
    public function markAsRead(Notification $notification): JsonResponse
    {
        $user = $this->getUser();

        // Vérifier que la notification appartient à l'utilisateur connecté
        if ($notification->getUser() !== $user) {
            return new JsonResponse(['success' => false, 'message' => 'Notification non trouvée'], 403);
        }

        $this->notificationService->markAsRead($notification);

        return new JsonResponse(['success' => true]);
    }

    #[Route('/mark-all-as-read', name: 'app_notifications_mark_all_as_read', methods: ['POST'])]
    public function markAllAsRead(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['success' => false], 403);
        }

        $this->notificationService->markAllAsRead($user);

        return new JsonResponse(['success' => true]);
    }
}
