<?php
// src/Service/TwilioSmsApiService.php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class TwilioSmsApiService
{
    public function __construct(
        private HttpClientInterface $client,
        private string $accountSid,
        private string $authToken,
        private string $from
    ) {}

    public function sendSms(string $to, string $body): void
    {
        $url = sprintf(
            'https://api.twilio.com/2010-04-01/Accounts/%s/Messages.json',
            $this->accountSid
        );

        $response = $this->client->request('POST', $url, [
            'auth_basic' => [$this->accountSid, $this->authToken],
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'body' => [
                'From' => $this->from,
                'To' => $to,
                'Body' => $body,
            ],
        ]);

        $statusCode = $response->getStatusCode();
        if ($statusCode < 200 || $statusCode >= 300) {
            throw new \RuntimeException(
                'Erreur Twilio SMS: '.$response->getContent(false)
            );
        }
    }
}