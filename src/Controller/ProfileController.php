<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileEditType;
use App\Form\PasswordChangeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ProfileController extends AbstractController
{
    /**
     * Affiche le profil de l'utilisateur connecté
     */
    #[Route('/profile', name: 'frontoffice_profile')]
    public function profile(): Response
    {
        $user = $this->getUser();
        
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        
        return $this->render('frontoffice/profile.html.twig', [
            'user' => $user
        ]);
    }
    
    /**
     * Permet à l'utilisateur de modifier son profil et changer son mot de passe
     */
    #[Route('/profile/edit', name: 'frontoffice_profile_edit')]
    public function editProfile(
        Request $request, 
        EntityManagerInterface $entityManager, 
        SluggerInterface $slugger,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = $this->getUser();
        
        if (!$user instanceof User) {
            throw new AccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }
        
        $profileForm = $this->createForm(ProfileEditType::class, $user);
        $passwordForm = $this->createForm(PasswordChangeType::class);
        
        // Définir les chemins de téléchargement
        $profilePhotosDir = $this->getParameter('kernel.project_dir') . '/public/uploads/profile_photos';
        $cvsDir = $this->getParameter('kernel.project_dir') . '/public/uploads/cvs';
        
        // Créer les répertoires s'ils n'existent pas
        if (!file_exists($profilePhotosDir)) {
            mkdir($profilePhotosDir, 0777, true);
        }
        
        if (!file_exists($cvsDir)) {
            mkdir($cvsDir, 0777, true);
        }
        
        $profileForm->handleRequest($request);
        
        if ($profileForm->isSubmitted() && $profileForm->isValid()) {
            // Gestion de la photo de profil
            $photoFile = $profileForm->get('profilePhotoFile')->getData();
            
            if ($photoFile) {
                $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$photoFile->guessExtension();
                
                try {
                    $photoFile->move(
                        $profilePhotosDir,
                        $newFilename
                    );
                    
                    // Supprimer l'ancienne photo si elle existe
                    $oldPhoto = $user->getProfilePhoto();
                    if ($oldPhoto) {
                        $oldPhotoPath = $profilePhotosDir.'/'.$oldPhoto;
                        if (file_exists($oldPhotoPath)) {
                            unlink($oldPhotoPath);
                        }
                    }
                    
                    $user->setProfilePhoto($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors du téléchargement de votre photo de profil.');
                }
            }
            
            // Gestion du CV
            $cvFile = $profileForm->get('cvFile')->getData();
            
            if ($cvFile) {
                $originalFilename = pathinfo($cvFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$cvFile->guessExtension();
                
                try {
                    $cvFile->move(
                        $cvsDir,
                        $newFilename
                    );
                    
                    // Supprimer l'ancien CV s'il existe
                    $oldCv = $user->getCv();
                    if ($oldCv) {
                        $oldCvPath = $cvsDir.'/'.$oldCv;
                        if (file_exists($oldCvPath)) {
                            unlink($oldCvPath);
                        }
                    }
                    
                    $user->setCv($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors du téléchargement de votre CV.');
                }
            }
            
            $entityManager->persist($user);
            $entityManager->flush();
            
            $this->addFlash('success', 'Votre profil a été mis à jour avec succès.');
            
            return $this->redirectToRoute('frontoffice_profile');
        }
        
        $passwordForm->handleRequest($request);
        
        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $data = $passwordForm->getData();
            
            // Vérifier l'ancien mot de passe
            if (!$passwordHasher->isPasswordValid($user, $data['currentPassword'])) {
                $this->addFlash('error', 'Le mot de passe actuel est incorrect.');
                
                return $this->redirectToRoute('frontoffice_profile_edit');
            }
            
            // Vérifier que le nouveau mot de passe et la confirmation sont identiques
            if (isset($data['confirmPassword']) && $data['newPassword'] !== $data['confirmPassword']) {
                $this->addFlash('error', 'Le nouveau mot de passe et sa confirmation ne correspondent pas.');
                
                return $this->redirectToRoute('frontoffice_profile_edit');
            }
            
            // Mettre à jour le mot de passe
            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $data['newPassword']
                )
            );
            
            $entityManager->persist($user);
            $entityManager->flush();
            
            $this->addFlash('success', 'Votre mot de passe a été modifié avec succès.');
            
            return $this->redirectToRoute('frontoffice_profile');
        }
        
        return $this->render('frontoffice/profile_edit.html.twig', [
            'user' => $user,
            'profileForm' => $profileForm->createView(),
            'passwordForm' => $passwordForm->createView()
        ]);
    }
}