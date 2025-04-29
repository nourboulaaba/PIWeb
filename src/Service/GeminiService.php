<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

class GeminiService
{
    private HttpClientInterface $client;
    private string $apiUrl;
    private string $apiKey;
    private LoggerInterface $logger;  // Declare the logger

    public function __construct(HttpClientInterface $client, string $apiUrl, string $apiKey, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->apiUrl = $apiUrl;
        $this->apiKey = $apiKey;
        $this->logger = $logger;  // Initialize the logger
    }

    public function classifyReclamation(string $sujet, string $description): string
    {
        // Log the received inputs
        $this->logger->info('Sujet: ' . $sujet);
        $this->logger->info('Description: ' . $description);

        // Prepare the content for the Gemini API
        $content = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => "Classifie cette réclamation en 'Urgente', 'Moyenne' ou 'Simple' :\nSujet : $sujet\nDescription : $description"]
                    ]
                ]
            ]
        ];

        try {
            // Make the request to the Gemini API
            $response = $this->client->request('POST', $this->apiUrl . '?key=' . $this->apiKey, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => $content,
            ]);

            // Get the response as an array
            $data = $response->toArray();

            // Log the API response
            $this->logger->info('API Response: ' . print_r($data, true));

            // Check for the text in the response
            if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                return trim($data['candidates'][0]['content']['parts'][0]['text']);
            }

            return "Non classifiée";
        } catch (\Exception $e) {
            // Log the error if the API call fails
            $this->logger->error('Error calling API: ' . $e->getMessage());
            return "Erreur lors de l'appel à l'API : " . $e->getMessage();
        }
    }
}
