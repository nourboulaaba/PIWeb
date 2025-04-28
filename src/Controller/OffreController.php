<?php

namespace App\Controller;

use App\Entity\Departement;
use App\Entity\Offre;
use App\Form\OffreForDepartementType;
use App\Form\OffreType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\GeminiAiService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;

#[Route('/offre')]
class OffreController extends AbstractController
{
    #[Route('/', name: 'app_offre_index', methods: ['GET'])]
    public function index(Request $request,EntityManagerInterface $entityManager): Response
    {

        $page = max(1, (int) $request->query->get('page', 1));
        $limit = 3;
        $offset = ($page - 1) * $limit;

        $query = $entityManager->createQuery(
            'SELECT o FROM App\Entity\Offre o ORDER BY o.id DESC'
        )
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        $paginator = new Paginator($query, true);
        $total = count($paginator);
        $totalPages = ceil($total / $limit);

        return $this->render('offre/index.html.twig', [
            'offres' => $paginator,
            'currentPage' => $page,
            'totalPages' => $totalPages,
        ]);
    }

    #[Route('/new', name: 'app_offre_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $offre = new Offre();
        $form = $this->createForm(OffreType::class, $offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($offre);
            $entityManager->flush();
            $this->addFlash('success', 'Offre ajoutée avec succès !');


            //return $this->redirectToRoute('app_offre_index', [], Response::HTTP_SEE_OTHER);

        }

        return $this->renderForm('offre/new.html.twig', [
            'offre' => $offre,
            'form' => $form,
        ]);

    }
    // Add a new route to handle offer creation specifically for a department
    #[Route('/new/{departementId}', name: 'app_offre_new_for_departement', methods: ['GET', 'POST'])]
    public function newForDepartement(Request $request, EntityManagerInterface $entityManager, int $departementId): Response
    {
        // Fetch the department by its ID
        $departement = $entityManager->getRepository(Departement::class)->find($departementId);

        if (!$departement) {
            return $this->redirectToRoute('app_departement_index');
        }

        // Create a new offer
        $offre = new Offre();
        // Set the department automatically
        $offre->setDepartement($departement);

        // Create the form with the custom type (without department field)
        $form = $this->createForm(OffreForDepartementType::class, $offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($offre);
            $entityManager->flush();

            return $this->redirectToRoute('app_departement_offres', ['id' => $departement->getId()]);
        }

        return $this->renderForm('offre/new_for_departement.html.twig', [
            'form' => $form,
            'departement' => $departement,
        ]);
    }



    #[Route('/ai/description', name: 'app_ai_generate_description', methods: ['POST'])]
    public function generateDescription(Request $request, GeminiAiService $geminiAi): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $title = $data['titre'] ?? '';
        $current = $data['currentDescription'] ?? '';

        if (!$title) {
            return new JsonResponse(['error' => 'Titre requis'], 400);
        }

        try {
            $suggestion = $geminiAi->generateDescription($title, $current);
            return new JsonResponse(['suggestion' => $suggestion ?? '']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Erreur AI: ' . $e->getMessage()], 500);
        }
    }



    #[Route('/offre/{id}/qr-code', name: 'app_offre_qrcode', methods: ['GET'])]
    public function generateQrCode(Offre $offre): Response
    {
        // Properly encode title and description for URL
        $title = urlencode($offre->getTitre());
        $description = urlencode($offre->getDescription());

        // Generate the direct link to be opened via QR scan
        $url = "https://parisfrance2424g7g7gtt.on.drv.tw/AbderrahmenOffreQRCode/offer.html?title={$title}&description={$description}";

        // Build QR code from the full URL
        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($url)
            ->size(300)
            ->margin(10)
            ->build();

        return new Response($result->getString(), 200, [
            'Content-Type' => 'image/png',
        ]);
    }

    #[Route('/freelancers', name: 'app_freelancers', methods: ['GET'])]
    public function listFreelancers(HttpClientInterface $httpClient): Response
    {
        try {
            $response = $httpClient->request('GET', 'https://freelancer-api.p.rapidapi.com/api/find-freelancers', [
                'headers' => [
                    'x-rapidapi-host' => 'freelancer-api.p.rapidapi.com',
                    'x-rapidapi-key' => '0ab87892fcmsh8a9a0820b2fa42bp16c751jsne4a0b3ec2abb',
                ],
            ]);

            $data = $response->toArray();
            $freelancers = $this->processFreelancersData($data['freelancers'] ?? []);

        } catch (\Exception $e) {
            $this->addFlash('danger', 'Error fetching freelancers: ' . $e->getMessage());
            $freelancers = [];
        }

        return $this->render('offre/freelancers.html.twig', [
            'freelancers' => $freelancers,
        ]);
    }

    private function processFreelancersData(array $freelancers): array
    {
        return array_map(function($freelancer) {
            // Clean up duplicate reviews text
            $reviews = preg_split('/\s+/', $freelancer['reviews'] ?? '');
            $reviewCount = count($reviews) > 0 ? $reviews[0] : 0;

            return [
                'name' => $freelancer['name'] ?? 'Anonymous Freelancer',
                'hourRating' => $freelancer['hourRating'] ?? 'Rate not specified',
                'reviews' => $reviewCount . ' reviews',
                'earnings' => number_format((float)($freelancer['earnings'] ?? 0), 1),
                'stars' => number_format((float)($freelancer['stars'] ?? 0), 1),
                'skills' => $this->processSkills($freelancer['skills'] ?? ''),
                'bio' => $freelancer['bio'] ?? 'No biography available',
                'freelancerProfile' => $freelancer['freelancerProfile'] ?? '#',
            ];
        }, $freelancers);
    }

    private function processSkills(string $skills): string
    {
        // Remove duplicate skills and limit to 8 skills
        $skillsArray = array_unique(explode(', ', $skills));
        return implode(', ', array_slice($skillsArray, 0, 8));
    }



    #[Route('/search', name: 'app_offre_search', methods: ['GET'])]
    public function search(Request $request, EntityManagerInterface $entityManager): Response
    {
        $query = $request->query->get('q', '');

        $offres = $entityManager->getRepository(Offre::class)
            ->createQueryBuilder('o')
            ->join('o.departement', 'd')
            ->where('LOWER(o.titre) LIKE LOWER(:q)')
            ->orWhere('LOWER(o.description) LIKE LOWER(:q)')
            ->orWhere('LOWER(d.nom) LIKE LOWER(:q)')
            ->setParameter('q', '%' . $query . '%')
            ->getQuery()
            ->getResult();

        return $this->render('offre/_table.html.twig', [
            'offres' => $offres,
        ]);
    }

    #[Route('/{id}', name: 'app_offre_show', methods: ['GET'])]
    public function show(Offre $offre): Response
    {
        return $this->render('offre/show.html.twig', [
            'offre' => $offre,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_offre_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Offre $offre, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OffreType::class, $offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_offre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('offre/edit.html.twig', [
            'offre' => $offre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_offre_delete', methods: ['POST'])]
    public function delete(Request $request, Offre $offre, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$offre->getId(), $request->request->get('_token'))) {
            $entityManager->remove($offre);
            $entityManager->flush();
            // Flash message for successful deletion
            $this->addFlash('success', 'Offre supprimée avec succès !');
            //debug line

        }

        return $this->redirectToRoute('app_offre_index', [], Response::HTTP_SEE_OTHER);
    }
}
