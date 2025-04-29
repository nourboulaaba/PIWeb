<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\CongeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class EmployeeController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/calendar', name: 'employee_calendar', methods: ['GET'])]
    public function calendarView(Request $request): Response
    {
        $userId = $request->query->get('userId');

        if (!$userId) {
            return $this->render('calendar/error.html.twig', [
                'message' => 'No user ID provided.',
            ]);
        }

        return $this->render('calendar/index.html.twig', [
            'userId' => $userId,
        ]);
    }

    // This method fetches events for a given userId and returns them as JSON
    #[Route('/calendar', name: 'employee_calendar_events', methods: ['POST'])]
    public function calendarEvents(
        Request $request,
        CongeRepository $congeRepository,
        ValidatorInterface $validator
    ): JsonResponse {
        $userId = $request->request->get('userId');

        // Validate userId (it should not be blank)
        $violations = $validator->validate(['userId' => $userId], new Assert\Collection([
            'userId' => new Assert\NotBlank(['message' => 'User ID cannot be blank.']),
        ]));

        if (count($violations) > 0) {
            return new JsonResponse(['error' => 'User ID is required.'], 400);
        }

        // Check if the user exists in the database
        $user = $this->entityManager->getRepository(User::class)->find($userId);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found.'], 404);
        }

        // Fetch the user's conges from the CongeRepository
        $conges = $congeRepository->findBy(['employe' => $user]);

        if (empty($conges)) {
            return new JsonResponse(['error' => 'No conges found for this user.'], 404);
        }

        // Calculate the leave balance (starting from 30 days)
        $initialLeaveBalance = 30;
        $leaveDaysTaken = 0;

        foreach ($conges as $conge) {
            if ($conge->getStatut() === 'Accepté') {
                $leaveDaysTaken += $conge->getDateDebut()->diff($conge->getDateFin())->days;
            }
        }

        $congeSolde = $initialLeaveBalance - $leaveDaysTaken;

        // Prepare the events array with conges data
        $events = [];
        foreach ($conges as $conge) {
            $status = $conge->getStatut();

            // Set color based on 'statut' value
            $color = $this->getEventColor($status);

            $events[] = [
                'title' => 'Congé',
                'start' => $conge->getDateDebut()->format('Y-m-d'),
                'end' => $conge->getDateFin()->modify('+1 day')->format('Y-m-d'),
                'allDay' => true,
                'color' => $color,
            ];
        }

        // Return the events and leave balance (congé solde)
        return new JsonResponse([
            'events' => $events,
            'congeSolde' => $congeSolde,
        ]);
    }

    // Helper method to return event color based on statut
    private function getEventColor(string $status): string
    {
        switch ($status) {
            case 'Accepté':
                return 'green'; // Green color for accepted
            case 'Refusé':
                return 'red'; // Red color for rejected
            case 'En attente':
                return 'yellow'; // Yellow color for pending
            default:
                return 'blue'; // Default color if statut is unknown
        }
    }
}
