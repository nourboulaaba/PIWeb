<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\FormationRepository;
use App\Entity\Formation;

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

    #[Route('/formations', name: 'app_front_formation_index')]
    public function formations(FormationRepository $formationRepository): Response
    {
        return $this->render('frontoffice/formations/index.html.twig', [
            'formations' => $formationRepository->findAll(),
        ]);
    }

    #[Route('/formations/{id}', name: 'app_front_formation_show')]
    public function formationShow(Formation $formation): Response
    {
        return $this->render('frontoffice/formations/show.html.twig', [
            'formation' => $formation,
        ]);
    }
}
