<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/test')]
class TestApiController extends AbstractController
{
    #[Route('', name: 'api_test_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return new JsonResponse([
            'success' => true,
            'message' => 'API de test fonctionne correctement',
            'timestamp' => (new \DateTime())->format('Y-m-d H:i:s'),
            'routes' => [
                'formations' => '/api/formations',
                'formations_test' => '/api/formations/test',
                'formations_stats' => '/api/formations/stats',
                'certificats' => '/api/certificats',
                'certificats_test' => '/api/certificats/test',
                'certificats_stats' => '/api/certificats/stats'
            ]
        ]);
    }
}
