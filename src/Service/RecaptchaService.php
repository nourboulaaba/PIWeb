<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class RecaptchaService
{
    private string $secretKey;
    private string $siteKey;

    public function __construct(string $secretKey, string $siteKey)
    {
        $this->secretKey = $secretKey;
        $this->siteKey = $siteKey;
    }

    public function getSiteKey(): string
    {
        return $this->siteKey;
    }

    /**
     * Vérifie si le captcha est valide
     *
     * @param string $recaptchaResponse
     * @return bool
     */
    public function verify(string $recaptchaResponse): bool
    {
        if (empty($recaptchaResponse)) {
            return false;
        }

        try {
            $client = HttpClient::create();
            $response = $client->request('POST', 'https://www.google.com/recaptcha/api/siteverify', [
                'body' => [
                    'secret' => $this->secretKey,
                    'response' => $recaptchaResponse
                ]
            ]);

            $data = $response->toArray();
            
            return $data['success'] ?? false;
        } catch (TransportExceptionInterface $e) {
            
            return false;
        }
    }
}
