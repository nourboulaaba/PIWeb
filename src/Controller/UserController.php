<?php
// src/Controller/UserController.php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UserController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user, ['is_edit' => false]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle file uploads
            $this->handleFileUploads($form, $user);

            // Hash the password
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur inscrit avec succès !');

            return $this->redirectToRoute('app_user_list');
        }

        return $this->render('user/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
    #[Route('/signup', name: 'app_signup')]
public function signup(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
{
    $user = new User();
    $form = $this->createForm(UserType::class, $user, ['is_edit' => false]);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $this->handleFileUploads($form, $user);

        $user->setPassword(
            $passwordHasher->hashPassword(
                $user,
                $form->get('password')->getData()
            )
        );

        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success', 'Compte créé avec succès !');

        return $this->redirectToRoute('app_signup');
    } elseif ($form->isSubmitted()) {
        $this->addFlash('error', 'Erreur lors de la création du compte. Vérifiez vos informations.');
    }

    return $this->render('registration/signup.html.twig', [
        'registrationForm' => $form->createView(),
    ]);
}


    #[Route('/users', name: 'app_user_list')]
    public function list(EntityManagerInterface $entityManager, Request $request, PaginatorInterface $paginator): Response
    {
        $query = $entityManager->getRepository(User::class)->createQueryBuilder('u')->getQuery();

        $users = $paginator->paginate(
            $query, // Requête à paginer
            $request->query->getInt('page', 1), // Numéro de page, 1 par défaut
            5 // Nombre d'éléments par page
        );

        return $this->render('user/list.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/user/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/user/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user, ['is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle file uploads
            $this->handleFileUploads($form, $user);

            // Update password if changed
          
            $entityManager->flush();

            $this->addFlash('success', 'User updated successfully!');

            return $this->redirectToRoute('app_user_list');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/user/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();

            $this->addFlash('success', 'User deleted successfully!');
        }

        return $this->redirectToRoute('app_user_list');
    }

    private function handleFileUploads($form, User $user): void
    {
        $uploadDir = $this->getParameter('kernel.project_dir').'/public/uploads/';

        // Handle CV upload
        /** @var UploadedFile $cvFile */
        $cvFile = $form->get('cv')->getData();
        if ($cvFile) {
            $cvFileName = uniqid().'.'.$cvFile->guessExtension();
            $cvFile->move($uploadDir.'cvs/', $cvFileName);
            $user->setCv($cvFileName);
        }

        // Handle profile photo upload
        /** @var UploadedFile $photoFile */
        $photoFile = $form->get('profile_photo')->getData();
        if ($photoFile) {
            $photoFileName = uniqid().'.'.$photoFile->guessExtension();
            $photoFile->move($uploadDir.'profile_photos/', $photoFileName);
            $user->setProfilePhoto($photoFileName);
        }
    }
}
