<?php

namespace App\Controller;

use App\Entity\Application;
use App\Entity\Entretien;
use App\Entity\Recrutement;
use App\Entity\User;
use App\Form\ApplicationType;
use App\Service\GeminiApiService;
use App\Service\PdfExtractorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/application')]
final class ApplicationController extends AbstractController
{

    #[Route('/apply/{recrutementId}', name: 'app_apply', methods: ['GET','POST'])]
    public function apply(
        int $recrutementId,
        Request $request,
        EntityManagerInterface $em,
        GeminiApiService $geminiApiService,
        PdfExtractorService $pdfExtractorService
    ): Response {
        // TEMP USER (Replace with $this->getUser() for real login)
        $user = $em->getRepository(User::class)->find(1);

        if (!$user) {
            return $this->json(['message' => 'User not found.'], Response::HTTP_NOT_FOUND);
        }

        // File handling
        $file = $request->files->get('resume');
        if (!$file) {
            return $this->json(['message' => 'No file uploaded.'], Response::HTTP_BAD_REQUEST);
        }

        if ($file->getClientOriginalExtension() !== 'pdf') {
            return $this->json(['message' => 'Only PDF files are allowed.'], Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
        }

        // Save the file
        $destination = $this->getParameter('kernel.project_dir') . '/public/uploads/resumes';
        $filename = uniqid() . '.' . $file->guessExtension();
        $file->move($destination, $filename);

        $filePath = $destination . '/' . $filename;
        $cvText = $pdfExtractorService->extractTextFromPdf($filePath);

        // Find the recrutement
        $recrutement = $em->getRepository(Recrutement::class)->find($recrutementId);
        if (!$recrutement) {
            return $this->json(['message' => 'Recrutement not found.'], Response::HTTP_NOT_FOUND);
        }

        // Get offer description
        $offer = $recrutement->getOffre();
        $offerDescription = $offer->getDescription();

        // Calculate ATS score
        $atsScore = $geminiApiService->getAtSScore($cvText, $offerDescription);

        // Find an entretien related to this recrutement
        $entretien = $em->getRepository(Entretien::class)->findOneBy(['recrutement' => $recrutement]);
        if (!$entretien) {
            return $this->json(['message' => 'No entretien available for this recrutement.'], Response::HTTP_NOT_FOUND);
        }

        // Create and persist Application
        $application = new Application();
        $application->setUser($user);
        $application->setEntretien($entretien);
        $application->setCvPath($filename); // Save only the filename
        $application->setAtsScore($atsScore);

        $em->persist($application);
        $em->flush();

        return $this->json(['message' => 'Application submitted successfully!'], Response::HTTP_OK);
    }


    #[Route(name: 'app_application_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $applications = $entityManager
            ->getRepository(Application::class)
            ->findAll();

        return $this->render('application/index.html.twig', [
            'applications' => $applications,
        ]);
    }

    #[Route('/new', name: 'app_application_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $application = new Application();
        $form = $this->createForm(ApplicationType::class, $application);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($application);
            $entityManager->flush();

            return $this->redirectToRoute('app_application_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('application/new.html.twig', [
            'application' => $application,
            'form' => $form,
        ]);
    }






    #[Route('/{id}', name: 'app_application_show', methods: ['GET'])]
    public function show(Application $application): Response
    {
        return $this->render('application/show.html.twig', [
            'application' => $application,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_application_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Application $application, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ApplicationType::class, $application);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_application_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('application/edit.html.twig', [
            'application' => $application,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_application_delete', methods: ['POST'])]
    public function delete(Request $request, Application $application, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$application->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($application);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_application_index', [], Response::HTTP_SEE_OTHER);
    }
}
