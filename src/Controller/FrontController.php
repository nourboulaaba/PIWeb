<?php

namespace App\Controller;

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
}
