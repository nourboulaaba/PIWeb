<?php

namespace App\Controller;

use App\Repository\ApplicationRepository;
use App\Repository\EntretienRepository;
use App\Repository\RecrutementRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{
    #[Route('/accueil', name: 'app_front_index')]
    public function index(): Response
    {
        return $this->render('basefront.html.twig');
    }

    #[Route('/profile', name: 'frontoffice_profile')]
    public function profile(): Response
    {
        return $this->render('frontoffice/profile.html.twig');
    }


 /*   #[Route('/front', name: 'app_front')]
    public function index(): Response
    {
        return $this->render('front/index.html.twig', [
            'controller_name' => 'FrontController',
        ]);
    }

*/
    #[Route('/positions', name: 'front_positions')]
    public function positions(RecrutementRepository $recrutementRepository, ApplicationRepository $applicationRepository,UserRepository $userRepository): Response
    {
        // For testing, hardcode user ID to 1
        $user = $userRepository->find(7);

        if (!$user) {
            // Handle case if no user is logged in (this could redirect to login or something else)
            return $this->redirectToRoute('app_login');
        }

        // Get all the recrutements (job offers)
        $recrutements = $recrutementRepository->findAll();

        // Get all applications for the user (user ID = 1)
        $applications = $applicationRepository->findBy(['user' => $user]);

        // Extract the recrutement IDs that the user has applied for
        $appliedRecrutementsIds = array_map(function($application) {
            return $application->getRecrutement()->getId();
        }, $applications);

        return $this->render('front/recrutement_list.html.twig', [
            'recrutements' => $recrutements,
            'appliedRecrutementsIds' => $appliedRecrutementsIds,
        ]);
    }

    #[Route('/my-interviews', name: 'front_my_interviews')]
    public function myInterviews(EntretienRepository $entretienRepository,UserRepository $userRepository): Response
    {
        // For testing purposes - hardcode user ID 1
        $userId = 1;

        return $this->render('front/user_entretiens.html.twig', [
            'entretiens' => $entretienRepository->findBy(['user' => $userRepository->find($userId)])
        ]);
         }
   
}
