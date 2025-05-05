<?php
// src/Service/PdfGeneratorService.php
namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Twig\Environment;

class PdfGeneratorService
{
    private const GEMINI_API_URL = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';
    private const GEMINI_API_KEY = 'AIzaSyCWYXrEGlHeFdgJ-HEgeP-d-53vxgsxCso';

    private $httpClient;
    private $twig;

    public function __construct(HttpClientInterface $httpClient, Environment $twig)
    {
        $this->httpClient = $httpClient;
        $this->twig = $twig;
    }

    public function generateStatsPdf(array $stats, string $htmlContent): Response
    {
        $statsString = $this->convertStatsToString($stats);
        $aiInsights = $this->getGeminiInsights($statsString);

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($pdfOptions);
        $dompdf->loadHtml(str_replace('<!-- AI_INSIGHTS -->', $aiInsights, $htmlContent));
        $dompdf->render();

        return new Response(
            $dompdf->output(),
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="recruitment_stats.pdf"'
            ]
        );
    }

    private function convertStatsToString(array $stats): string
    {
        $output = [
            'Recruitment Statistics Report',
            '',
            'Basic Metrics:',
            sprintf("- Total Processes: %d", $stats['totalRecrutements']),
            sprintf("- Total Interviews: %d", $stats['totalEntretiens']),
            sprintf("- Approval Rate: %.1f%%", ($stats['approvalStats']['approved'] / $stats['totalEntretiens']) * 100),
            '',
            'Location Distribution:'
        ];



        $output[] = '';
        $output[] = 'Process Distribution:';
        foreach ($stats['recrutementStats'] as $process) {
            $output[] = sprintf("- %d interviews: %d processes", $process['NbEntretien'], $process['process_count']);
        }

        return implode("\n", $output);
    }

    private function getGeminiInsights(string $statsData): string
    {
        try {
            $url = sprintf('%s?key=%s', self::GEMINI_API_URL, self::GEMINI_API_KEY);

            $response = $this->httpClient->request('POST', $url, [
                'json' => [
                    'contents' => [
                        'parts' => [
                            ['text' => "Analyze these recruitment statistics and provide:
                        1. Exactly 3 key insights in bullet points
                        2. Exactly 3 recommendations in bullet points
                        Be concise and focus only on the most critical points.
                        Format as:
                        ### Insights:
                        - Insight 1
                        - Insight 2
                        - Insight 3
                        
                        ### Recommendations:
                        - Recommendation 1
                        - Recommendation 2
                        - Recommendation 3
                        
                        Statistics:\n\n$statsData"]
                        ]
                    ]
                ],
                'timeout' => 30
            ]);

            $content = $response->toArray();

            if (!isset($content['candidates'][0]['content']['parts'][0]['text'])) {
                throw new \RuntimeException('Invalid response structure from Gemini API');
            }

            $rawResponse = $content['candidates'][0]['content']['parts'][0]['text'];

            // Clean up the response
            $cleanedResponse = preg_replace('/\*\*(.*?)\*\*/', '$1', $rawResponse); // Remove bold formatting
            $cleanedResponse = preg_replace('/\s+/', ' ', $cleanedResponse); // Remove extra whitespace
            $cleanedResponse = trim($cleanedResponse);

            // Ensure proper formatting
            if (!str_contains($cleanedResponse, '### Insights:')) {
                $cleanedResponse = "### Insights:\n- " .
                    implode("\n- ", array_slice(explode("\n", $cleanedResponse), 0, 3)) .
                    "\n\n### Recommendations:\n- " .
                    implode("\n- ", array_slice(explode("\n", $cleanedResponse), 3, 3));
            }

            return $cleanedResponse;

        } catch (\Exception $e) {
            return sprintf(
                "### Insights:\n- Error retrieving AI insights\n\n### Recommendations:\n- Check API connection\n- Verify service status\n\nError details: %s",
                $e->getMessage()
            );
        }
    }


    public function generateDepartmentStatsPdf(array $departements, array $offres): Response
    {
        $stats = $this->calculateDepartmentStats($departements, $offres);
        $statsString = $this->convertDepartmentStatsToString($stats);
        $aiInsights = $this->getGeminiInsights($statsString);

        $html = $this->twig->render('departement/statistics_pdf.html.twig', [
            'departements' => $departements,
            'offres' => $offres,
            'stats' => $stats,
            'aiInsights' => $aiInsights,
            'date' => new \DateTime()
        ]);

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($pdfOptions);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return new Response(
            $dompdf->output(),
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/pdf'
            ]
        );
    }


    private function calculateDepartmentStats(array $departements, array $offres): array
    {
        $stats = [
            'totalDepartments' => count($departements),
            'totalOffers' => count($offres),
            'totalEmployees' => 0,
            'totalBudget' => 0,
            'avgSalaryRange' => ['min' => 0, 'max' => 0],
            'departmentDetails' => [],
            'offerDistribution' => []
        ];

        foreach ($departements as $dept) {
            $stats['totalEmployees'] += $dept->getNbEmploye();
            $stats['totalBudget'] += $dept->getBudget();
        }

        if (count($offres) > 0) {
            $totalMin = 0;
            $totalMax = 0;
            foreach ($offres as $offer) {
                $totalMin += $offer->getSalaireMin();
                $totalMax += $offer->getSalaireMax();
            }
            $stats['avgSalaryRange']['min'] = round($totalMin / count($offres));
            $stats['avgSalaryRange']['max'] = round($totalMax / count($offres));
        }

        foreach ($departements as $dept) {
            $deptOffers = array_filter($offres, function($offer) use ($dept) {
                return $offer->getDepartement()->getId() === $dept->getId();
            });

            $stats['departmentDetails'][] = [
                'name' => $dept->getNom(),
                'offerCount' => count($deptOffers),
                'employeeCount' => $dept->getNbEmploye(),
                'budget' => $dept->getBudget(),
                'manager' => $dept->getResponsable() ? $dept->getResponsable()->getFirst_name() : 'N/A'
            ];

            $stats['offerDistribution'][$dept->getNom()] = count($deptOffers);
        }

        return $stats;
    }

    private function convertDepartmentStatsToString(array $stats): string
    {
        $output = [
            'Department and Offer Statistics Report',
            '',
            'Summary Metrics:',
            sprintf("- Total Departments: %d", $stats['totalDepartments']),
            sprintf("- Total Job Offers: %d", $stats['totalOffers']),
            sprintf("- Total Employees: %d", $stats['totalEmployees']),
            sprintf("- Total Budget: %d €", $stats['totalBudget']),
            sprintf("- Average Salary Range: %d - %d €", 
                $stats['avgSalaryRange']['min'], 
                $stats['avgSalaryRange']['max']),
            '',
            'Department Breakdown:'
        ];

        foreach ($stats['departmentDetails'] as $dept) {
            $output[] = sprintf(
                "- %s: %d employees, %d offers, %d € budget",
                $dept['name'],
                $dept['employeeCount'],
                $dept['offerCount'],
                $dept['budget']
            );
        }

        $output[] = '';
        $output[] = 'Offer Distribution:';
        foreach ($stats['offerDistribution'] as $dept => $count) {
            $output[] = sprintf("- %s: %d offers (%.1f%%)", 
                $dept, 
                $count, 
                ($count / $stats['totalOffers']) * 100);
        }

        return implode("\n", $output);
    }
}