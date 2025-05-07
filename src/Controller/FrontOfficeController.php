<?php

// src/Controller/FrontOfficeController.php
// src/Controller/FrontOfficeController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\OffreRepository;
use App\Entity\Departement;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Tools\Pagination\Paginator;
class FrontOfficeController extends AbstractController
{


    private $entityManager;

    // Inject EntityManagerInterface via constructor
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    #[Route('/aceuill', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('frontoffice/index.html.twig');
    }



    

    #[Route('/offres', name: 'front_office_offres')]
    public function index1(Request $request, OffreRepository $offreRepository, PaginatorInterface $paginator)
    {
        $query = $request->query->get('q', '');
        $departementId = $request->query->get('departement', null);

        // Apply filters in the repository using QueryBuilder
        $queryBuilder = $offreRepository->findAll(); // custom QB method

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            5
        );

        $departements = $this->entityManager->getRepository(Departement::class)->findAll();

        return $this->render('front.html.twig', [
            'offres' => $pagination,
            'departements' => $departements,
            'query' => $query,
            'departementId' => $departementId,
        ]);
    }

}
