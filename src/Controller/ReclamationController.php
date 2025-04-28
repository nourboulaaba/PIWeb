<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Entity\User;
use App\Entity\Conge;
use App\Form\ReclamationType;
use App\Form\ReclamationFilterType;
use App\Repository\ReclamationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\GeminiService;  // Ajoutez ce service
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Validator\ValidatorInterface;


final class ReclamationController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private GeminiService $geminiService;


    // Combinez les deux constructeurs en un seul
    public function __construct(EntityManagerInterface $entityManager, GeminiService $geminiService)
    {
        $this->entityManager = $entityManager;
        $this->geminiService = $geminiService;  // Injecter GeminiService

    }

    #[Route('dashboard/reclamation/encours', name: 'app_reclamation_index', methods: ['GET'])]
    public function index(ReclamationRepository $reclamationRepository): Response
    {
        // Récupérer toutes les réclamations sauf celles ayant le statut "traité"
        $reclamations = $reclamationRepository->findBy([
            'statut' => ['Non traité', 'en cours'] // Exclure celles avec statut "traité"
        ]);

        return $this->render('reclamation/index.html.twig', [
            'reclamations' => $reclamations,
        ]);
    }

    #[Route('profile/reclamation/new', name: 'app_reclamation_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $error = null;
        $success = null;
        $classification = null;

        if ($request->isMethod('POST')) {
            $userId = $request->request->get('user_id');
            $congeId = $request->request->get('conge_id');
            $sujet = $request->request->get('sujet');
            $description = $request->request->get('description');
            $date = $request->request->get('date');
            $statut = $request->request->get('statut');

            if (!$userId) {
                $error = 'L\'ID utilisateur est requis.';
            } else {
                $user = $this->entityManager->getRepository(User::class)->find($userId);
                $conge = $this->entityManager->getRepository(Conge::class)->find($congeId);

                if (!$user) {
                    $error = 'Utilisateur introuvable.';
                } elseif (!$conge) {
                    $error = 'Congé introuvable.';
                } elseif ($conge->getEmploye()->getId() !== (int) $userId) {
                    $error = 'Ce congé n’appartient pas à l’utilisateur saisi.';
                } else {
                    // Utilisation de Gemini pour analyser la classification de la réclamation
                    $classification = $this->geminiService->classifyReclamation($sujet, $description);

                    // Créer la réclamation
                    $reclamation = new Reclamation();
                    $reclamation->setUser($user)
                        ->setConge($conge)
                        ->setSujet($sujet)
                        ->setDescription($description)
                        ->setDate(new \DateTime())
                        ->setStatut($statut ?: 'Non traité');

                    // Ajouter la classification à la réclamation
                    $reclamation->setClassification($classification);

                    $this->entityManager->persist($reclamation);
                    $this->entityManager->flush();

                    $success = 'Réclamation enregistrée avec succès.';
                }
            }
        }

        return $this->render('reclamation/new.html.twig', [
            'error' => $error,
            'success' => $success,
            'classification' => $classification,  // Afficher la classification dans la vue
        ]);
    }
    #[Route('/dashboard/reclamation/traites', name: 'app_reclamation_traites')]
    public function showTraites(RequestStack $requestStack, PaginatorInterface $paginator, Request $request): Response
    {
        // Create the filter form
        $filterForm = $this->createForm(ReclamationFilterType::class);

        // Handle form submission
        $filterForm->handleRequest($request);

        // Get the current page (default to 1 if not set)
        $page = $request->query->getInt('page', 1);

        // Start building the query to get "Traité" reclamations
        $queryBuilder = $this->entityManager->getRepository(Reclamation::class)
            ->createQueryBuilder('r')
            ->where('r.statut = :statut')
            ->setParameter('statut', 'Traité');  // Only 'Traité' reclamations

        // Apply filtering if the form is submitted and valid
        if ($filterForm->isSubmitted() && $filterForm->isValid()) {
            $data = $filterForm->getData();

            // Debug the form data to check what's being sent
            dump($data); // This will output the form data in the console (in dev mode)

            // If classification is selected, filter by classification
            if ($data['classification']) {
                $queryBuilder->andWhere('r.classification = :classification')
                    ->setParameter('classification', $data['classification']);
            }
        }

        // Get the query with the filters applied
        $query = $queryBuilder->getQuery();

        // Apply pagination to the query
        $pagination = $paginator->paginate(
            $query,  // The query to execute
            $page,   // Current page
            10       // Number of items per page
        );

        // Pass data to the template
        return $this->render('reclamation/traites.html.twig', [
            'pagination' => $pagination,
            'filter_form' => $filterForm->createView(),  // Pass form to the template
        ]);
    }



    #[Route('dashboard/reclamation/{id}/edit', name: 'app_reclamation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reclamation $reclamation): Response
    {
        if ($request->isMethod('POST')) {
            $statut = $request->get('statut');
            if ($statut) {
                $reclamation->setStatut($statut); // Mise à jour du statut
                $this->entityManager->flush();    // Sauvegarder les modifications
                $this->addFlash('success', 'Statut mis à jour avec succès!');
            }
            return $this->redirectToRoute('app_reclamation_index');
        }

        return $this->render('reclamation/edit.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }

    #[Route('/{id}', name: 'app_reclamation_delete', methods: ['POST'])]
    public function delete(Request $request, Reclamation $reclamation): Response
    {
        if ($this->isCsrfTokenValid('delete' . $reclamation->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($reclamation);
            $this->entityManager->flush();

            $this->addFlash('success', 'La réclamation a été supprimée avec succès !');
        }

        return $this->redirectToRoute('app_reclamation_index');
    }

    #[Route('profile/reclamation/search', name: 'app_reclamation_search', methods: ['GET'])]
    public function searchReclamationsByUser(Request $request, ReclamationRepository $reclamationRepository): Response
    {
        $userId = $request->query->get('userId');
        $errorMessage = '';
        $reclamations = [];

        if (!$userId) {
            $errorMessage = 'L\'ID de l\'utilisateur est requis.';
        } else {
            $user = $this->entityManager->getRepository(User::class)->find($userId);

            if (!$user) {
                $errorMessage = 'L\'utilisateur avec cet ID n\'existe pas.';
            } else {
                $reclamations = $reclamationRepository->findBy(['user' => $user]);

                if (empty($reclamations)) {
                    $errorMessage = 'Aucune réclamation trouvée pour cet utilisateur.';
                }
            }
        }

        return $this->render('reclamation/search.html.twig', [
            'reclamations' => $reclamations,
            'errorMessage' => $errorMessage,
            'userId' => $userId,
        ]);
    }

    #[Route('/dashboard/reclamation/{id}', name: 'app_reclamation_show', methods: ['GET'])]
    public function show(int $id, ReclamationRepository $reclamationRepository): Response
    {
        $reclamation = $reclamationRepository->find($id);

        if (!$reclamation) {
            throw $this->createNotFoundException('Réclamation introuvable.');
        }

        return $this->render('reclamation/show.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }
}
