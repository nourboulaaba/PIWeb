<?php
// src/Service/FreelancerApiService.php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class FreelancerApiService
{
    private HttpClientInterface $client;
    private string $apiKey = '0ab87892fcmsh8a9a0820b2fa42bp16c751jsne4a0b3ec2abb';

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function fetchFreelancers(): array
    {
        $response = $this->client->request('GET', 'https://freelancer-api.p.rapidapi.com/api/find-freelancers', [
            'headers' => [
                'x-rapidapi-host' => 'freelancer-api.p.rapidapi.com',
                'x-rapidapi-key' => $this->apiKey,
            ],
        ]);

        $data = $response->toArray();

        return $data['freelancers'] ?? [];
    }
}
