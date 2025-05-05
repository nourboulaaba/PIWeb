<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class GeminiAiService
{
    private HttpClientInterface $client;
    private string $apiKey = 'AIzaSyCWYXrEGlHeFdgJ-HEgeP-d-53vxgsxCso';

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function generateDescription(string $title, string $currentDescription = ''): ?string
    {
        $prompt = sprintf(
            "Rédige une description d'offre d'emploi concise et attrayante en français qui ne dépasse pas 100 mots pour le poste suivant : \"%s\"%s.",
            $title,
            $currentDescription ? " en tenant compte de cette description partielle : \"$currentDescription\"" : ""
        );

        try {
            $response = $this->client->request('POST', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent', [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'query' => [
                    'key' => $this->apiKey,
                ],
                'json' => [
                    'contents' => [
                        ['parts' => [['text' => $prompt]]],
                    ]
                ]
            ]);

            $data = $response->toArray(false);
            $rawText = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (!$rawText) return null;

            // Clean markdown and trim
            $cleaned = strip_tags(preg_replace('/[#*]+/', '', $rawText));
            return trim($cleaned);

        } catch (TransportExceptionInterface|\Exception $e) {
            // Log error or handle it
            return null;
        }
    }
}
