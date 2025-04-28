<?php

namespace App\Controller;

use App\Entity\Departement;
use App\Entity\Offre;
use App\Form\DepartementType;
use App\Repository\DepartementRepository;
use App\Repository\OffreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;

#[Route('/departement')]
class DepartementController extends AbstractController
{

    #[Route('/', name: 'app_departement_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $departements = $entityManager
            ->getRepository(Departement::class)
            ->findAll();

        return $this->render('departement/index.html.twig', [
            'departements' => $departements,
        ]);
    }


    // Symfony controller to handle the chart image upload
    public function uploadChart(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        // Get the base64 chart data and chart name
        $chartData = $data['chartData'];
        $chartName = $data['chartName'];

        // Extract base64 data (remove the data:image/png;base64, part)
        $base64Image = preg_replace('#^data:image/\w+;base64,#i', '', $chartData);
        $imageData = base64_decode($base64Image);

        // Save the image to the public/uploads/charts directory
        $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads/charts/';
        $imagePath = $uploadDir . $chartName . '.png';

        // Ensure the directory exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Save the image
        file_put_contents($imagePath, $imageData);

        return new JsonResponse([
            'message' => 'Image uploaded successfully',
            'imagePath' => '/uploads/charts/' . $chartName . '.png',
        ]);
    }



    private function createAndSavePdf(string $html, string $filename = 'stats_charts.pdf'): string
    {
        $options = new Options();
        $options->set('defaultFont', 'Helvetica');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $pdfOutput = $dompdf->output();
        $pdfPath = $this->getParameter('kernel.project_dir') . '/public/uploads/' . $filename;
        file_put_contents($pdfPath, $pdfOutput);

        return '/uploads/' . $filename;
    }

// Add this method inside DepartementController
    #[Route('/departements/stats/pdf', name: 'departement_stats_pdf', methods: ['POST'])]
    // Symfony controller to generate the PDF with images
    public function generateStatsPdf(Request $request)
    {
        // Use the saved images in the PDF
        $chart1Path = '/uploads/charts/budgetSalaryChart.png';
        $chart2Path = '/uploads/charts/salaryRangeChart.png';
        $chart3Path = '/uploads/charts/employeeCountChart.png';
        $chart4Path = '/uploads/charts/departmentBudgetChart.png';
        $chart5Path = '/uploads/charts/departmentEmployeeCountChart.png';

        // Generate the PDF content
        $html = '<h2>Statistical Charts</h2>';
        $html .= '<div><img src="' . $chart1Path . '" style="width: 100%; max-height: 600px;"></div>';
        $html .= '<div><img src="' . $chart2Path . '" style="width: 100%; max-height: 600px;"></div>';
        $html .= '<div><img src="' . $chart3Path . '" style="width: 100%; max-height: 600px;"></div>';
        $html .= '<div><img src="' . $chart4Path . '" style="width: 100%; max-height: 600px;"></div>';
        $html .= '<div><img src="' . $chart5Path . '" style="width: 100%; max-height: 600px;"></div>';


        // Generate the PDF (e.g., using DomPDF or KnpSnappyBundle)
        $pdfUrl = $this->createAndSavePdf($html, 'stats_charts.pdf');

        return new JsonResponse([
            'message' => 'PDF generated successfully.',
            'pdfUrl' => $pdfUrl,
        ]);
    }




    #[Route('/departement/statistics/pdf', name: 'departement_generate_pdf')]
    public function generatePdf(OffreRepository $offreRepo, DepartementRepository $departementRepo): JsonResponse
    {
        $offres = $offreRepo->findAll();
        $departements = $departementRepo->findAll();

        $html = $this->renderView('departement/statistics_pdf.html.twig', [
            'offres' => $offres,
            'departements' => $departements,
        ]);

        $pdfUrl = $this->createAndSavePdf($html, 'departement_statistics.pdf');

        return new JsonResponse([
            'message' => 'PDF generated successfully.',
            'pdfUrl' => $pdfUrl,
        ]);
    }




    #[Route('/stats', name: 'app_departement_stats')]
    public function stats(EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        $departements = $em->getRepository(Departement::class)->findAll();
        $offres = $em->getRepository(Offre::class)->findAll();

        return $this->render('departement/stats.html.twig', [
            'departements' => $departements,
            'offres' => $offres,
        ]);
    }

    #[Route('/new', name: 'app_departement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $departement = new Departement();
        $form = $this->createForm(DepartementType::class, $departement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($departement);
            $entityManager->flush();
            $this->addFlash('success', 'Département ajouté avec succès !');
           // return $this->redirectToRoute('app_departement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('departement/new.html.twig', [
            'departement' => $departement,
            'form' => $form,
        ]);
    }

    #[Route('/search', name: 'app_departement_search', methods: ['GET'])]
    public function search(Request $request, EntityManagerInterface $entityManager): Response
    {
        $query = $request->query->get('q', '');

        $departements = $entityManager->getRepository(Departement::class)
            ->createQueryBuilder('d')
            ->where('LOWER(d.nom) LIKE LOWER(:search)')
            ->setParameter('search', '%' . $query . '%')
            ->getQuery()
            ->getResult();

        return $this->render('departement/_table.html.twig', [
            'departements' => $departements,
        ]);
    }

    #[Route('/{id}', name: 'app_departement_show', methods: ['GET'])]
    public function show(Departement $departement): Response
    {
        return $this->render('departement/show.html.twig', [
            'departement' => $departement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_departement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Departement $departement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DepartementType::class, $departement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_departement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('departement/edit.html.twig', [
            'departement' => $departement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/offres', name: 'app_departement_offres', methods: ['GET'])]
    public function offresByDepartement(Departement $departement): Response
    {
        return $this->render('departement/offres.html.twig', [
            'departement' => $departement,
            'offres' => $departement->getOffres(),
        ]);
    }


    #[Route('/{id}', name: 'app_departement_delete', methods: ['POST'])]
    public function delete(Request $request, Departement $departement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$departement->getId(), $request->request->get('_token'))) {
            $entityManager->remove($departement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_departement_index', [], Response::HTTP_SEE_OTHER);
    }






}
