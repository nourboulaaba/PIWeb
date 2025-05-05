<?php
namespace App\Controller;

use App\Entity\Formation;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Response;

class PaymentController extends AbstractController
{
    #[Route('/front/payment', name: 'front_payment_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('frontoffice/payment/index.html.twig');
    }

    #[Route('/payment/session', name: 'payment_session', methods: ['POST'])]
    public function createSession(Request $request, EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $formation = $em->getRepository(Formation::class)->find($data['formationId']);

        if (!$formation) {
            return new JsonResponse(['error' => 'Formation non trouvée'], 404);
        }

        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        $session = Session::create([
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
            'success_url' => $urlGenerator->generate('front_payment_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $urlGenerator->generate('front_payment_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        return new JsonResponse(['id' => $session->id]);
    }

    #[Route('/front/payment/success', name: 'front_payment_success', methods: ['GET'])]
    public function success(): Response
    {
        return $this->render('frontoffice/payment/success.html.twig');
    }

    #[Route('/front/payment/cancel', name: 'front_payment_cancel', methods: ['GET'])]
    public function cancel(): Response
    {
        return $this->render('frontoffice/payment/cancel.html.twig');
    }
}
