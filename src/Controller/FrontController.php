<?php

namespace App\Controller;

use App\Repository\ApplicationRepository;
use App\Repository\EntretienRepository;
use App\Repository\RecrutementRepository;
use App\Repository\UserRepository;
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

