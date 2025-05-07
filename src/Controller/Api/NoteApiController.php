<?php

namespace App\Controller\Api;

use App\Entity\Formation;
use App\Entity\Note;
use App\Repository\NoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/notes')]
class NoteApiController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private NoteRepository $noteRepository;
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;

    public function __construct(
        EntityManagerInterface $entityManager,
        NoteRepository $noteRepository,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ) {
        $this->entityManager = $entityManager;
        $this->noteRepository = $noteRepository;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    #[Route('/formation/{id}', name: 'api_notes_formation', methods: ['GET'])]
    public function getFormationNotes(Formation $formation): JsonResponse
    {
        $notes = $this->noteRepository->findByFormation($formation);
        $moyenne = $this->noteRepository->calculateAverageRating($formation);
        $count = $this->noteRepository->countRatings($formation);

        $data = [
            'formation' => [
                'id' => $formation->getId(),
                'name' => $formation->getName(),
            ],
            'moyenne' => $moyenne,
            'nombreAvis' => $count,
            'notes' => []
        ];

        foreach ($notes as $note) {
            $data['notes'][] = [
                'id' => $note->getId(),
                'valeur' => $note->getValeur(),
                'commentaire' => $note->getCommentaire(),
                'dateCreation' => $note->getDateCreation()->format('Y-m-d H:i:s'),
                'utilisateur' => [
                    'id' => $note->getUser()->getId(),
                    'nom' => $note->getUser()->getLastName(),
                    'prenom' => $note->getUser()->getFirstName(),
                ]
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('/formation/{id}', name: 'api_notes_add', methods: ['POST'])]
    public function addNote(Request $request, Formation $formation): JsonResponse
    {
        $user = $this->getUser();
        
        if (!$user) {
            return new JsonResponse(['message' => 'Vous devez être connecté pour noter une formation'], Response::HTTP_UNAUTHORIZED);
        }

        // Vérifier si l'utilisateur a déjà noté cette formation
        if ($this->noteRepository->hasUserRated($formation, $user->getId())) {
            return new JsonResponse(['message' => 'Vous avez déjà noté cette formation'], Response::HTTP_BAD_REQUEST);
        }

        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['valeur']) || !is_numeric($data['valeur']) || $data['valeur'] < 1 || $data['valeur'] > 5) {
            return new JsonResponse(['message' => 'La note doit être comprise entre 1 et 5'], Response::HTTP_BAD_REQUEST);
        }

        $note = new Note();
        $note->setFormation($formation);
        $note->setUser($user);
        $note->setValeur($data['valeur']);
        
        if (isset($data['commentaire'])) {
            $note->setCommentaire($data['commentaire']);
        }

        $errors = $this->validator->validate($note);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($note);
        $this->entityManager->flush();

        return new JsonResponse([
            'message' => 'Note ajoutée avec succès',
            'note' => [
                'id' => $note->getId(),
                'valeur' => $note->getValeur(),
                'commentaire' => $note->getCommentaire(),
                'dateCreation' => $note->getDateCreation()->format('Y-m-d H:i:s')
            ]
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'api_notes_delete', methods: ['DELETE'])]
    public function deleteNote(Note $note): JsonResponse
    {
        $user = $this->getUser();
        
        if (!$user) {
            return new JsonResponse(['message' => 'Vous devez être connecté pour supprimer une note'], Response::HTTP_UNAUTHORIZED);
        }

        // Vérifier si l'utilisateur est le propriétaire de la note ou un administrateur
        if ($note->getUser()->getId() !== $user->getId() && !in_array('ROLE_ADMIN', $user->getRoles())) {
            return new JsonResponse(['message' => 'Vous n\'êtes pas autorisé à supprimer cette note'], Response::HTTP_FORBIDDEN);
        }

        $this->entityManager->remove($note);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Note supprimée avec succès']);
    }
}
