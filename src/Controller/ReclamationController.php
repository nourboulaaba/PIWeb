<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Entity\User;
use App\Form\ReclamationType;
use App\Repository\ReclamationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ReclamationController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
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
        $reclamation = new Reclamation();
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer l'ID utilisateur saisi
            $userId = $form->get('userId')->getData();
            $user = $this->entityManager->getRepository(User::class)->find($userId);

            if (!$user) {
                $this->addFlash('error', 'Aucun utilisateur trouvé avec cet ID.');
                return $this->render('reclamation/new.html.twig', [
                    'form' => $form->createView(),
                    'reclamation' => $reclamation,
                ]);
            }

            // Associer l'utilisateur
            $reclamation->setUser($user);

            // Définir la date actuelle si non fournie
            if (!$reclamation->getDate()) {
                $reclamation->setDate(new \DateTime());
            }

            $this->entityManager->persist($reclamation);
            $this->entityManager->flush();

            $this->addFlash('success', 'Votre réclamation a été enregistrée avec succès !');

            return $this->redirectToRoute('app_reclamation_index');
        }

        return $this->render('reclamation/new.html.twig', [
            'form' => $form->createView(),
            'reclamation' => $reclamation,
        ]);
    }

    #[Route('/dashboard/reclamation/traites', name: 'app_reclamation_traites')]
    public function showTraites(): Response
    {
        $reclamations = $this->entityManager->getRepository(Reclamation::class)->findBy([
            'statut' => 'traité'
        ]);
    
        return $this->render('reclamation/traites.html.twig', [
            'reclamations' => $reclamations,
        ]);
    }
    

    #[Route('dashboard/reclamation/{id}/edit', name: 'app_reclamation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reclamation $reclamation): Response
    {
        // Vérifiez si l'utilisateur souhaite changer le statut
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
