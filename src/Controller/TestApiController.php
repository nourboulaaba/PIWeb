<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/test-api')]
class TestApiController extends AbstractController
{
    #[Route('', name: 'app_test_api_index')]
    public function index(): Response
    {
        return $this->render('test_api/index.html.twig');
    }
}
