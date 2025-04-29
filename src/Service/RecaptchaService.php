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
    public function verify(?string $recaptchaResponse): bool
    {
        // Si la réponse est vide, retourner false immédiatement
        if (empty($recaptchaResponse)) {
            return false;
        }

        try {
            // Créer un client HTTP
            $client = HttpClient::create();

            // Faire la requête à l'API Google reCAPTCHA
            $response = $client->request('POST', 'https://www.google.com/recaptcha/api/siteverify', [
                'body' => [
                    'secret' => $this->secretKey,
                    'response' => $recaptchaResponse
                ]
            ]);

            // Récupérer la réponse sous forme de tableau
            $data = $response->toArray();

            // Si la vérification a échoué, on peut logger les erreurs
            if (!($data['success'] ?? false)) {
                $errorCodes = $data['error-codes'] ?? ['unknown-error'];
                // Ici, vous pourriez ajouter un logger pour enregistrer les erreurs
                // $this->logger->error('reCAPTCHA verification failed', ['error-codes' => $errorCodes]);
            }

            // Retourner le résultat de la vérification
            return $data['success'] ?? false;
        } catch (TransportExceptionInterface $e) {
            // En cas d'erreur de transport, on pourrait logger l'erreur
            // $this->logger->error('reCAPTCHA verification transport error', ['message' => $e->getMessage()]);
            return false;
        } catch (\Exception $e) {
            // En cas d'erreur générale, on pourrait logger l'erreur
            // $this->logger->error('reCAPTCHA verification error', ['message' => $e->getMessage()]);
            return false;
        }
    }
}
