<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/logs')]
class LogsController extends AbstractController
{
    #[Route('', name: 'app_logs')]
    public function index(): Response
    {
        return $this->render('logs/index.html.twig');
    }
}
