<?php
// src/Service/CertifierService.php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class CertifierService
{
    private $client;
    private $apiToken;
    private $groupId;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
        $this->apiToken = 'TON_TOKEN_API'; // Remplace ici
        $this->groupId = 'TON_GROUP_ID';   // Remplace ici
    }

    public function sendCertificate(string $name, string $email): array
    {
        $response = $this->client->request('POST', 'https://api.certifier.me/v1/credentials', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Certifier-Version' => '2022-10-26'
            ],
            'json' => [
                'groupId' => $this->groupId,
                'recipient' => [
                    'name' => $name,
                    'email' => $email,
                ],
                'issueDate' => date('Y-m-d'),
                'expiryDate' => date('Y-m-d', strtotime('+1 year')),
            ],
        ]);

        return $response->toArray();
    }
}
