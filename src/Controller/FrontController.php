<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\FormationRepository;
use App\Entity\Formation;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use App\Form\FormationSearchType;

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
    public function formations(
        Request $request, 
        FormationRepository $formationRepository,
        PaginatorInterface $paginator
    ): Response
    {
        // Créer le formulaire de recherche
        $form = $this->createForm(FormationSearchType::class);
        $form->handleRequest($request);
        
        // Construire la requête de base
        $queryBuilder = $formationRepository->createQueryBuilder('f');
        
        // Appliquer les filtres si le formulaire est soumis
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            
            // Filtre par mot-clé
            if (!empty($data['keyword'])) {
                $queryBuilder
                    ->andWhere('f.name LIKE :keyword OR f.description LIKE :keyword')
                    ->setParameter('keyword', '%' . $data['keyword'] . '%');
            }
            
            // Filtre par prix minimum
            if (!empty($data['prix_min'])) {
                $queryBuilder
                    ->andWhere('f.prix >= :prix_min')
                    ->setParameter('prix_min', $data['prix_min']);
            }
            
            // Filtre par prix maximum
            if (!empty($data['prix_max'])) {
                $queryBuilder
                    ->andWhere('f.prix <= :prix_max')
                    ->setParameter('prix_max', $data['prix_max']);
            }
            
            // Tri
            if (!empty($data['sort_by'])) {
                switch ($data['sort_by']) {
                    case 'prix_asc':
                        $queryBuilder->orderBy('f.prix', 'ASC');
                        break;
                    case 'prix_desc':
                        $queryBuilder->orderBy('f.prix', 'DESC');
                        break;
                    case 'date_desc':
                        $queryBuilder->orderBy('f.date', 'DESC');
                        break;
                    case 'date_asc':
                        $queryBuilder->orderBy('f.date', 'ASC');
                        break;
                }
            }
        }
        
        // Pagination
        $formations = $paginator->paginate(
            $queryBuilder->getQuery(),
            $request->query->getInt('page', 1),
            6 // Nombre d'éléments par page
        );
        
        return $this->render('frontoffice/formations/index.html.twig', [
            'formations' => $formations,
            'searchForm' => $form->createView(),
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

