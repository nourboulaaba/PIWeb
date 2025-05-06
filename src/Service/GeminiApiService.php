<?php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

class GeminiApiService
{
    private $httpClient;
    private $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=AIzaSyCWYXrEGlHeFdgJ-HEgeP-d-53vxgsxCso';

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Get the ATS score by comparing the CV and the job offer description.
     *
     * @param string $cvText The extracted text from the CV
     * @param string $offerDescription The job offer description
     *
     * @return float The ATS score
     */
    public function getAtSScore(string $cvText, string $offerDescription): float
    {
        $prompt = "Given the following CV text and job offer description, return only the ATS matching score between 0 and 1.\n\n" .
            "CV Text:\n" . $cvText . "\n\n" .
            "Job Offer Description:\n" . $offerDescription . "\n\n" .
            "Response: Please provide only the numeric ATS score.";

        $body = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ]
        ];

        try {
            $response = $this->httpClient->request('POST', $this->apiUrl, [
                'json' => $body,
            ]);

            $data = $response->toArray();

            return $this->extractScore($data);
        } catch (\Exception $e) {
            throw new HttpException(500, 'Error communicating with Gemini API: ' . $e->getMessage());
        }
    }


    /**
     * Extract the ATS score from the Gemini API response.
     *
     * @param array $response The response data from the Gemini API
     *
     * @return float The ATS score
     */
    private function extractScore(array $response): float
    {
        $text = $response['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if ($text !== null && is_numeric(trim($text))) {
            return (float) trim($text);
        }

        throw new HttpException(500, 'Unable to retrieve ATS score from Gemini API response.');
    }

}
