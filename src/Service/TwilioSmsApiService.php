<?php // src/Service/TwilioSmsApiService.php
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
            // auth_basic gère l’HTTP Basic Auth
            'auth_basic' => [$this->accountSid, $this->authToken],
            'body'       => [
                'From' => $this->from,
                'To'   => $to,
                'Body' => $body,
            ],
        ]);

        // Vérifier le succès (201 Created ou 200 OK selon Twilio)
        if (!in_array($response->getStatusCode(), [200, 201], true)) {
            throw new \RuntimeException(
                'Erreur Twilio SMS: '.$response->getContent(false)
            );
        }
    }
}
