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

#[Route('/reclamation')]
final class ReclamationController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'app_reclamation_index', methods: ['GET'])]
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

    #[Route('/new', name: 'app_reclamation_new', methods: ['GET', 'POST'])]
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

    #[Route('/{id}', name: 'app_reclamation_show', methods: ['GET'])]
    public function show(Reclamation $reclamation): Response
    {
        return $this->render('reclamation/show.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }


    #[Route('/reclamation/{id}/edit', name: 'app_reclamation_edit', methods: ['GET', 'POST'])]
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

    #[Route('/user/{userId}', name: 'app_reclamation_by_user', methods: ['GET'])]
    public function getByUser(
        int $userId,
        ReclamationRepository $reclamationRepository,
        ValidatorInterface $validator
    ): Response {
        $errorMessage = '';
        $reclamations = [];

        $idConstraint = new Assert\Positive(['message' => 'L\'ID utilisateur doit être un nombre positif.']);
        $violations = $validator->validate($userId, $idConstraint);

        if (count($violations) > 0) {
            $errorMessage = 'ID utilisateur invalide.';
        } else {
            $user = $this->entityManager->getRepository(User::class)->find($userId);

            if (!$user) {
                $errorMessage = 'Utilisateur non trouvé.';
            } else {
                $reclamations = $reclamationRepository->findBy(['user' => $userId]);

                if (empty($reclamations)) {
                    $errorMessage = 'Aucune réclamation trouvée pour cet utilisateur.';
                }
            }
        }

        return $this->render('reclamation/user_reclamations.html.twig', [
            'reclamations' => $reclamations,
            'userId' => $userId,
            'errorMessage' => $errorMessage,
        ]);
    }
    #[Route('/reclamation/traites', name: 'app_reclamation_traites')]
    public function showTraites(): Response
    {
        // Récupérer toutes les réclamations avec le statut "traité"
        $reclamations = $this->entityManager->getRepository(Reclamation::class)->findBy([
            'statut' => 'traité'
        ]);

        // Renvoyer la liste des réclamations traitées dans la vue
        return $this->render('reclamation/traites.html.twig', [
            'reclamations' => $reclamations,
        ]);
    }
}
