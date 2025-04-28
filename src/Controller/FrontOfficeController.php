<?php

// src/Controller/FrontOfficeController.php
namespace App\Controller;

use App\Repository\OffreRepository;
use App\Entity\Departement;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FrontOfficeController extends AbstractController
{
    private $entityManager;

    // Inject EntityManagerInterface via constructor
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/offres', name: 'front_office_offres')]
    public function index(Request $request, OffreRepository $offreRepository)
    {
        // Fetch the search query and department filter from the request
        $query = $request->query->get('q', '');
        $departementId = $request->query->get('departement', null);

        // Fetch filtered offers
        $offres = $offreRepository->findByFilters($query, $departementId);

        // Fetch departments to display in the filter dropdown using injected EntityManager
        $departements = $this->entityManager->getRepository(Departement::class)->findAll();

        return $this->render('front.html.twig', [
            'offres' => $offres,
            'departements' => $departements,
            'query' => $query,
            'departementId' => $departementId,
        ]);
    }
}
