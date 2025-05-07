<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Entity\Note;
use App\Form\NoteType;
use App\Repository\NoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/front/formation/note')]
class FrontNoteController extends AbstractController
{
    #[Route('/{id}/new', name: 'app_front_note_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Formation $formation, EntityManagerInterface $entityManager, NoteRepository $noteRepository): Response
    {
        $user = $this->getUser();
        
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour noter une formation.');
            return $this->redirectToRoute('app_login');
        }

        // Vérifier si l'utilisateur a déjà noté cette formation
        if ($noteRepository->hasUserRated($formation, $user->getId())) {
            $this->addFlash('warning', 'Vous avez déjà noté cette formation.');
            return $this->redirectToRoute('app_front_formation_show', ['id' => $formation->getId()]);
        }

        $note = new Note();
        $note->setFormation($formation);
        $note->setUser($user);
        
        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($note);
            $entityManager->flush();

            $this->addFlash('success', 'Votre note a été ajoutée avec succès !');
            return $this->redirectToRoute('app_front_formation_show', ['id' => $formation->getId()]);
        }

        return $this->render('frontoffice/note/new.html.twig', [
            'formation' => $formation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_front_note_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Note $note, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour modifier une note.');
            return $this->redirectToRoute('app_login');
        }

        // Vérifier si l'utilisateur est le propriétaire de la note
        if ($note->getUser()->getId() !== $user->getId()) {
            $this->addFlash('error', 'Vous n\'êtes pas autorisé à modifier cette note.');
            return $this->redirectToRoute('app_front_formation_show', ['id' => $note->getFormation()->getId()]);
        }

        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Votre note a été modifiée avec succès !');
            return $this->redirectToRoute('app_front_formation_show', ['id' => $note->getFormation()->getId()]);
        }

        return $this->render('frontoffice/note/edit.html.twig', [
            'note' => $note,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_front_note_delete', methods: ['POST'])]
    public function delete(Request $request, Note $note, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour supprimer une note.');
            return $this->redirectToRoute('app_login');
        }

        // Vérifier si l'utilisateur est le propriétaire de la note ou un administrateur
        if ($note->getUser()->getId() !== $user->getId() && !in_array('ROLE_ADMIN', $user->getRoles())) {
            $this->addFlash('error', 'Vous n\'êtes pas autorisé à supprimer cette note.');
            return $this->redirectToRoute('app_front_formation_show', ['id' => $note->getFormation()->getId()]);
        }

        if ($this->isCsrfTokenValid('delete'.$note->getId(), $request->getPayload()->getString('_token'))) {
            $formationId = $note->getFormation()->getId();
            $entityManager->remove($note);
            $entityManager->flush();
            
            $this->addFlash('success', 'Note supprimée avec succès.');
            return $this->redirectToRoute('app_front_formation_show', ['id' => $formationId]);
        }

        return $this->redirectToRoute('app_front_formation_show', ['id' => $note->getFormation()->getId()]);
    }
}
