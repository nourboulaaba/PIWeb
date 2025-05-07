<?php
// src/Service/StripeService.php
namespace App\Service;

use App\Entity\Formation;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class StripeService
{
    private string $privateKey;
    private UrlGeneratorInterface $router;

    public function __construct(string $secretKey, UrlGeneratorInterface $router)
    {
        $this->privateKey = $secretKey;
        $this->router = $router;

        Stripe::setApiKey($this->privateKey);
    }

    public function createSession(int $amountCents, string $currency, string $successUrl, string $cancelUrl): Session
    {
        return Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => $currency,
                    'product_data' => ['name' => 'Achat formation'],
                    'unit_amount' => $amountCents,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $successUrl,
            'cancel_url'  => $cancelUrl,
        ]);
    }

    public function createCheckoutSession(Formation $formation): Session
    {
        return Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $formation->getName(),
                    ],
                    'unit_amount' => $formation->getPrix() * 100,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $this->router->generate('front_payment_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->router->generate('front_payment_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);
    }
}
