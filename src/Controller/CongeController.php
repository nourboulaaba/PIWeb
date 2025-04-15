<?php

namespace App\Controller;

use App\Entity\Conge;
use App\Entity\User;
use App\Form\CongeType;
use App\Repository\CongeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry; // Ensure this is imported
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/conge')]
final class CongeController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    // Injection de EntityManagerInterface via le constructeur
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/conge', name: 'app_conge_index', methods: ['GET'])]
    public function index(CongeRepository $congeRepository): Response
    {
        // Récupérer tous les congés avec le statut 'En attente'
        $congesEnAttente = $congeRepository->findBy(['statut' => 'En attente']);

        return $this->render('conge/index.html.twig', [
            'conges' => $congesEnAttente,
        ]);
    }

    #[Route('/new', name: 'app_conge_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $conge = new Conge();
        $form = $this->createForm(CongeType::class, $conge);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer l'utilisateur à partir du formulaire
            $userId = $form->get('employe')->getData();
            $user = $entityManager->getRepository(User::class)->find($userId);

            if (!$user) {
                throw $this->createNotFoundException('Utilisateur non trouvé');
            }

            // Lier le congé à l'utilisateur
            $conge->setEmploye($user);
            $entityManager->persist($conge);
            $entityManager->flush();
            $this->addFlash('success', 'Votre demande de congé a été envoyée avec succès !');

            return $this->redirectToRoute('app_conge_index');
        }

        return $this->render('conge/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }



    #[Route('/conge/update/{id}', name: 'app_conge_update_statut', methods: ['POST'])]
    public function updateStatut(int $id, CongeRepository $congeRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        // Trouver le congé par ID
        $conge = $congeRepository->find($id);

        if (!$conge) {
            throw $this->createNotFoundException('Le congé n\'existe pas.');
        }

        // Récupérer l'action depuis la requête
        $action = $request->get('action');
        if ($action === 'accept') {
            $conge->setStatut('Accepté');
        } elseif ($action === 'reject') {
            $conge->setStatut('Refusé');
        }

        // Sauvegarder le changement
        $entityManager->flush();

        // Ajouter un message de succès
        $this->addFlash('success', 'Le statut du congé a été mis à jour.');

        // Rediriger vers la page d'index
        return $this->redirectToRoute('app_conge_index');
    }

    #[Route('/{idConges}', name: 'app_conge_delete', methods: ['POST'])]
    public function delete(Request $request, Conge $conge, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$conge->getIdConges(), $request->request->get('_token'))) {
            $entityManager->remove($conge);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_conge_index');
    }
    #[Route('/all', name: 'app_conge_index_tous', methods: ['GET'])]
    public function indexTous(CongeRepository $congeRepository): Response
    {
        $conges = $congeRepository->findAll();

        return $this->render('conge/show.html.twig', [
            'conges' => $conges,
            'statut' => 'Tous les congés'
        ]);
    }
    #[Route('/search', name: 'app_conge_search', methods: ['GET'])]
    public function searchCongesByEmploye(Request $request, CongeRepository $congeRepository, ValidatorInterface $validator): Response
    {
        $idEmploye = $request->query->get('idEmploye'); // Récupère l'ID de l'employé
        $conges = [];
        $errorMessage = '';

        // Validation de l'ID avec Assert
        $idEmployeConstraint = new Assert\NotBlank(['message' => 'L\'ID de l\'employé ne peut pas être vide.']);
        $idEmployeConstraints = new Assert\Collection([
            'idEmploye' => $idEmployeConstraint,
        ]);
        
        $violations = $validator->validate(['idEmploye' => $idEmploye], $idEmployeConstraints);

        // Si l'ID est vide ou invalide
        if (count($violations) > 0) {
            $errorMessage = 'L\'ID de l\'employé est obligatoire.';
        } else {
            // Vérifie si l'ID existe dans la table User
            $user = $this->entityManager->getRepository(User::class)->find($idEmploye);

            if (!$user) {
                $errorMessage = 'L\'ID de l\'employé n\'existe pas.';
            } else {
                // Si l'employé existe, cherche les congés de cet employé
                $conges = $congeRepository->findBy(['employe' => $user]);

                if (empty($conges)) {
                    $errorMessage = 'Aucun congé trouvé pour cet employé.';
                }
            }
        }

        return $this->render('conge/search.html.twig', [
            'conges' => $conges,
            'idEmploye' => $idEmploye, // Passe l'ID de l'employé à la vue
            'errorMessage' => $errorMessage, // Passe le message d'erreur à la vue
        ]);
    }
    

}


