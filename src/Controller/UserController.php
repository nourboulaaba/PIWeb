<?php
// src/Controller/UserController.php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }
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
    // Création d'un nouvel utilisateur
    $user = new User();

    // Création du formulaire d'inscription en passant l'option 'is_edit' à false
    $form = $this->createForm(RegistrationType::class, $user, ['is_edit' => false]);

    // Traitement du formulaire
    $form->handleRequest($request);

    // Déboguer les données du formulaire
    if ($form->isSubmitted()) {
        // Vérifier si le formulaire est valide
        if (!$form->isValid()) {
            // Afficher les erreurs du formulaire
            foreach ($form->getErrors(true) as $error) {
                $this->addFlash('error', $error->getMessage());
            }


        }
    }

    if ($form->isSubmitted() && $form->isValid()) {
        try {
            // Hash du mot de passe avant l'enregistrement
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            // Définir l'utilisateur comme non vérifié
            $user->setIsVerified(false);

            // Définir le rôle par défaut comme EMPLOYE
            $user->setRole('EMPLOYE');

            // Définir un salaire par défaut (requis car le champ est non nullable)
            $user->setSalary(0.0);

            // Nous n'utilisons pas de token de vérification d'email pour le moment
            // Le token est géré par le service EmailVerifier

            // Sauvegarde de l'utilisateur dans la base de données
            $entityManager->persist($user);
            $entityManager->flush();

            // Ajouter un message flash pour confirmer l'enregistrement dans la base de données
            $this->addFlash('info', 'Utilisateur enregistré avec succès dans la base de données. ID: ' . $user->getId());

            // Envoyer l'email de vérification
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('boulaabanour2020@gmail.com', 'TrueMatch'))
                    ->to($user->getEmail())
                    ->subject('Confirmation de votre adresse email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
        } catch (\Exception $e) {
            // En cas d'erreur, ajouter un message flash
            $this->addFlash('error', 'Erreur lors de l\'enregistrement de l\'utilisateur: ' . $e->getMessage());

            // Retourner à la page d'inscription
            return $this->render('registration/signup.html.twig', [
                'registrationForm' => $form->createView(),
            ]);
        }

        // Flash message de succès avec plus de détails
        $this->addFlash('success', 'Félicitations ! Votre compte a été créé avec succès. <br><br>Un email de vérification a été envoyé à <strong>' . $user->getEmail() . '</strong>. <br><br>Veuillez cliquer sur le lien dans cet email pour vérifier votre adresse email. Ensuite, un administrateur devra activer votre compte avant que vous puissiez vous connecter.');

        // Rediriger vers la page de connexion après inscription
        return $this->redirectToRoute('app_login');
    }

    // Si le formulaire n'est pas validé ou soumis, on le renvoie à la vue
    return $this->render('registration/signup.html.twig', [
        'registrationForm' => $form->createView(),
    ]);
}



    #[Route('/users', name: 'app_user_list')]
    public function list(Request $request, UserRepository $userRepository, PaginatorInterface $paginator): Response
    {
        // Récupérer les critères de recherche
        $criteria = [
            'search' => $request->query->get('search'),
            'role' => $request->query->get('role'),
            'is_verified' => $request->query->get('is_verified'),
            'sort_by' => $request->query->get('sort_by'),
            'sort_dir' => $request->query->get('sort_dir')
        ];

        // Obtenir le QueryBuilder avec les critères appliqués
        $queryBuilder = $userRepository->findBySearchCriteria($criteria);

        // Paginer les résultats
        $users = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 5)
        );

        // Récupérer les statistiques pour le dashboard
        $roleStats = $userRepository->getUserStatsByRole();
        $salaryStats = $userRepository->getAverageSalaryByRole();

        return $this->render('user/list.html.twig', [
            'users' => $users,
            'roleStats' => $roleStats,
            'salaryStats' => $salaryStats,
            'criteria' => $criteria
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
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
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
