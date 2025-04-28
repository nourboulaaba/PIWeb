<?php

namespace App\Controller;

use App\Entity\Certificat;
use App\Form\CertificatType;
use App\Repository\CertificatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\CertifierService;

#[Route('/certificat')]
final class CertificatController extends AbstractController
{
    #[Route(name: 'app_certificat_index', methods: ['GET'])]
    public function index(CertificatRepository $certificatRepository): Response
    {
        return $this->render('certificat/index.html.twig', [
            'certificats' => $certificatRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_certificat_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, CertifierService $certifierService): Response
    {
        $certificat = new Certificat();
        $form = $this->createForm(CertificatType::class, $certificat);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($certificat);
            $entityManager->flush();
    
            // 👇 ENVOYER à Certifier.me APRÈS enregistrement local
            try {
                $response = $certifierService->sendCertificate(
                    $certificat->getNom(),     // Il faut avoir un champ "Nom" sur ton certificat
                    $certificat->getEmail()    // Il faut avoir un champ "Email" aussi
                );
    
                $this->addFlash('success', 'Certificat enregistré et envoyé avec succès !');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de l\'envoi du certificat : ' . $e->getMessage());
            }
    
            return $this->redirectToRoute('app_certificat_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('certificat/new.html.twig', [
            'certificat' => $certificat,
            'form' => $form,
        ]);
    }

    #[Route('/{idCertif}', name: 'app_certificat_show', methods: ['GET'])]
    public function show(Certificat $certificat): Response
    {
        return $this->render('certificat/show.html.twig', [
            'certificat' => $certificat,
        ]);
    }

    #[Route('/{idCertif}/edit', name: 'app_certificat_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Certificat $certificat, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CertificatType::class, $certificat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_certificat_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('certificat/edit.html.twig', [
            'certificat' => $certificat,
            'form' => $form,
        ]);
    }

    #[Route('/{idCertif}', name: 'app_certificat_delete', methods: ['POST'])]
    public function delete(Request $request, Certificat $certificat, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$certificat->getIdCertif(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($certificat);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_certificat_index', [], Response::HTTP_SEE_OTHER);
    }
}
