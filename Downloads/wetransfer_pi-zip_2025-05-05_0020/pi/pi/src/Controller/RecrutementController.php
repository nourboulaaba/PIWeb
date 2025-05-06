<?php

namespace App\Controller;

use App\Entity\Application;
use App\Entity\Entretien;
use App\Entity\Offre;
use App\Entity\Recrutement;
use App\Entity\User;
use App\Form\EntretienType;
use App\Form\OffreType;
use App\Form\RecrutementType;
use App\Repository\ApplicationRepository;
use App\Repository\EntretienRepository;
use App\Repository\OffreRepository;
use App\Repository\RecrutementRepository;
use App\Repository\UserRepository;
use App\Service\GeminiApiService;
use App\Service\PdfExtractorService;
use App\Service\PdfGeneratorService;
use App\Service\SmsService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;


#[Route('/recrutement')]
class RecrutementController extends AbstractController
{
    #[Route('/', name: 'app_recrutement_index', methods: ['GET'])]
    public function index(Request $request,EntityManagerInterface $entityManager, OffreRepository $offreRepository, PaginatorInterface $paginator): Response
    {
        $query = $entityManager->getRepository(Recrutement::class)->findAll();

        $recrutements = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1), // page number
            5 // items per page
        );

        $offres = $offreRepository->findAll();

        return $this->render('recrutement/index.html.twig', [
            'recrutements' => $recrutements,
            'offres' => $offres,
        ]);
    }

    #[Route('/stat', name: 'app_recrutement_stat', methods: ['GET'])]
    public function stat(
        RecrutementRepository $recrutementRepository,
        EntretienRepository $entretienRepository,
        UserRepository $userRepository
    ): Response {
        // Basic counts
        $totalRecrutements = $recrutementRepository->count([]);
        $totalEntretiens = $entretienRepository->count([]);

        // Interview approval distribution
        $approvedCount = $entretienRepository->count(['approved' => true]);
        $pendingCount = $totalEntretiens - $approvedCount;

        // Simple user list without interview counts
        $users = $userRepository->findBy([], ['name' => 'ASC']);

        // Location distribution (simple count per location)
        $locationStats = $entretienRepository->createQueryBuilder('e')
            ->select('e.lieu', 'COUNT(e.id) as count')
            ->groupBy('e.lieu')
            ->orderBy('count', 'DESC')
            ->getQuery()
            ->getResult();

        // Interview count distribution per recruitment process
        $recrutementStats = $recrutementRepository->createQueryBuilder('r')
            ->select('r.NbEntretien', 'COUNT(r.id) as process_count')
            ->groupBy('r.NbEntretien')
            ->orderBy('r.NbEntretien', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('recrutement/stat.html.twig', [
            // Basic metrics
            'totalRecrutements' => $totalRecrutements,
            'totalEntretiens' => $totalEntretiens,

            // Distributions
            'approvalStats' => [
                'approved' => $approvedCount,
                'pending' => $pendingCount
            ],
            'users' => $users,  // Simple user list instead of stats
            'locationStats' => $locationStats,
            'recrutementStats' => $recrutementStats,
        ]);
    }

    #[Route('/stats/pdf', name: 'app_recrutement_stats_pdf')]
    public function generateStatsPdf(
        RecrutementRepository $recrutementRepository,
        EntretienRepository $entretienRepository,
        PdfGeneratorService $pdfGenerator
    ): Response {
        // Get statistics data
        $stats = [
            'totalRecrutements' => $recrutementRepository->count([]),
            'totalEntretiens' => $entretienRepository->count([]),
            'approvalStats' => [
                'approved' => $entretienRepository->count(['approved' => true]),
                'pending' => $entretienRepository->count(['approved' => false])
            ],
            'recrutementStats' => $recrutementRepository->createQueryBuilder('r')
                ->select('r.NbEntretien', 'COUNT(r.id) as process_count')
                ->groupBy('r.NbEntretien')
                ->getQuery()
                ->getResult()
        ];

        // Generate chart images
        $chartImages = [
            'approvalChart' => $this->generateChartImage($stats, 'approval'),
            'processChart' => $this->generateChartImage($stats, 'process')
        ];

        // Render HTML template
        $html = $this->renderView('recrutement/pdf_template.html.twig', [
            'stats' => $stats,
            'chartImages' => $chartImages,
            'generationDate' => new \DateTime()
        ]);

        return $pdfGenerator->generateStatsPdf($stats, $html);
    }

    private function generateChartImage(array $stats, string $chartType): string
    {
        $chartConfig = [];

        switch ($chartType) {
            case 'approval':
                $chartConfig = [
                    'type' => 'doughnut',
                    'data' => [
                        'labels' => ['Approved', 'Pending'],
                        'datasets' => [[
                            'data' => [
                                $stats['approvalStats']['approved'],
                                $stats['approvalStats']['pending']
                            ],
                            'backgroundColor' => ['#4CAF50', '#FFC107'],
                        ]]
                    ],
                    'options' => [
                        'plugins' => [
                            'legend' => ['position' => 'bottom'],
                            'title' => [
                                'display' => true,
                                'text' => 'Interview Approval Status'
                            ]
                        ]
                    ]
                ];
                break;

            case 'process':
                $labels = array_map(fn($p) => "{$p['NbEntretien']} interviews", $stats['recrutementStats']);
                $data = array_map(fn($p) => $p['process_count'], $stats['recrutementStats']);

                $chartConfig = [
                    'type' => 'bar',
                    'data' => [
                        'labels' => $labels,
                        'datasets' => [[
                            'label' => 'Number of Processes',
                            'data' => $data,
                            'backgroundColor' => '#3B82F6',
                        ]]
                    ],
                    'options' => [
                        'plugins' => [
                            'legend' => ['display' => false],
                            'title' => [
                                'display' => true,
                                'text' => 'Process Distribution by Interview Count'
                            ]
                        ],
                        'scales' => [
                            'y' => [
                                'beginAtZero' => true,
                                'ticks' => ['precision' => 0]
                            ]
                        ]
                    ]
                ];
                break;
        }

        try {
            $client = HttpClient::create();
            $response = $client->request('POST', 'https://quickchart.io/chart/create', [
                'json' => ['chart' => $chartConfig],
                'headers' => ['Content-Type' => 'application/json']
            ]);

            $data = $response->toArray();
            return $data['url']; // QuickChart returns a URL to the generated image

        } catch (\Exception $e) {
            // Fallback: Generate a placeholder if chart generation fails
            return 'data:image/svg+xml;base64,' . base64_encode(
                    '<svg xmlns="http://www.w3.org/2000/svg" width="400" height="200" viewBox="0 0 400 200">
                <rect width="100%" height="100%" fill="#f8f9fa"/>
                <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="#6c757d">
                    Chart unavailable
                </text>
            </svg>'
                );
        }
    }


    #[Route('/apply/{recrutementId}', name: 'app_apply', methods: ['GET', 'POST'])]
    public function apply(
        int $recrutementId,
        Request $request,
        EntityManagerInterface $em,
        GeminiApiService $geminiApiService,
        PdfExtractorService $pdfExtractorService,
        ApplicationRepository $applicationRepository
    ): Response {
        // TEMP USER (Replace with $this->getUser() for real login)
        $user = $em->getRepository(User::class)->find(1); // Hardcoded user ID 1 for testing

        if (!$user) {
            return $this->json(['message' => 'User not found.'], Response::HTTP_NOT_FOUND);
        }

        // Check if the user has already applied for this recrutement
        $existingApplication = $applicationRepository->findOneBy(['user' => $user, 'recrutement' => $recrutementId]);
        if ($existingApplication) {
            return $this->json(['message' => 'You have already applied for this position.'], Response::HTTP_BAD_REQUEST);
        }

        // File handling
        $file = $request->files->get('resume');
        if (!$file) {
            return $this->json(['message' => 'No file uploaded.'], Response::HTTP_BAD_REQUEST);
        }

        // Ensure only PDF files are uploaded
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

        // Get the offer description
        $offer = $recrutement->getOffre();
        $offerDescription = $offer->getDescription();

        // Calculate ATS score
        $atsScore = $geminiApiService->getAtSScore($cvText, $offerDescription);

        // Create and persist the Application
        $application = new Application();
        $application->setUser($user);
        $application->setRecrutement($recrutement);  // Link to recrutement
        $application->setCvPath($filename); // Save only the filename, not the full path
        $application->setAtsScore($atsScore);

        $em->persist($application);
        $em->flush();
       

        $userPhone = "+21650316723";
        $message = "Bonjour l'utilisateur " . $user->getFirst_name()."a uploader son cv pour le recrutement ".$recrutement->getOffre()->getTitre();
        $smsService->sendSms($userPhone, $message);

        return $this->json(['message' => 'Application submitted successfully!'], Response::HTTP_OK);
    }




    #[Route('/new', name: 'app_recrutement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $recrutement = new Recrutement();
        $form = $this->createForm(RecrutementType::class, $recrutement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($recrutement);
            $entityManager->flush();

            return $this->redirectToRoute('app_recrutement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('recrutement/new.html.twig', [
            'recrutement' => $recrutement,
            'form' => $form,
        ]);
    }

    #[Route('/search', name: 'app_recrutement_search', methods: ['GET'])]
    public function search(Request $request, RecrutementRepository $recrutementRepository,OffreRepository $offreRepository): Response
    {

        $query = $request->query->get('q', '');
        $recrutements = $recrutementRepository->findBySearchQuery($query);
        $offres = $offreRepository->findAll();


        // For AJAX requests, return only the table HTML
        if ($request->isXmlHttpRequest()) {
            return $this->render('recrutement/_table.html.twig', [
                'recrutements' => $recrutements,
                'offres' => $offres,
            ]);
        }

        // For regular requests, return full page
        return $this->render('recrutement/_table.html.twig', [
            'recrutements' => $recrutements,
            'offres' => $offres,
        ]);
    }

    #[Route('/{id}/entretiens', name: 'app_recrutement_entretiens')]
    public function entretiens(Recrutement $recrutement, Request $request, EntityManagerInterface $em,OffreRepository $offreRepository,ApplicationRepository $applicationRepository): Response
    {
        $entretien = new Entretien();
        $form = $this->createForm(EntretienType::class, $entretien);
        $offres=$offreRepository->findAll();
        $applications  = $applicationRepository->findByRecrutementId($recrutement->getId());


        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entretien->setRecrutement($recrutement);
            $em->persist($entretien);
            $em->flush();

            return $this->redirectToRoute('app_recrutement_entretiens', ['id' => $recrutement->getId()]);
        }

        return $this->render('recrutement/entretiens.html.twig', [
            'recrutement' => $recrutement,
            'entretiens' => $recrutement->getEntretiens(),
            'form' => $form->createView(),
            'offres' => $offres,
            'applications' => $applications,
        ]);
    }

    #[Route('/{id}', name: 'app_recrutement_show', methods: ['GET'])]
    public function show(Recrutement $recrutement,OffreRepository $offreRepository): Response
    {
        return $this->render('recrutement/show.html.twig', [
            'recrutement' => $recrutement,
            'offres' => $offreRepository->findAll(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_recrutement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Recrutement $recrutement, EntityManagerInterface $entityManager,OffreRepository $offreRepository): Response
    {
        $form = $this->createForm(RecrutementType::class, $recrutement);
        $form->handleRequest($request);
        $offres=$offreRepository->findAll();


        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_recrutement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('recrutement/editt.html.twig', [
            'recrutement' => $recrutement,
            'form' => $form,
            'offres' => $offres,
        ]);
    }

    #[Route('/{id}', name: 'app_recrutement_delete', methods: ['POST'])]
    public function delete(Request $request, Recrutement $recrutement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$recrutement->getId(), $request->request->get('_token'))) {
            $entityManager->remove($recrutement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_recrutement_index', [], Response::HTTP_SEE_OTHER);
    }


}
