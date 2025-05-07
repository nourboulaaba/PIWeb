<?php
namespace App\Controller;

use App\Entity\Formation;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Exception\CardException;
use Stripe\Exception\ApiErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\RouterInterface;

class PaymentController extends AbstractController
{
    private $security;
    
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/payment/form/{id}', name: 'payment_form', methods: ['GET'])]
    public function showPaymentForm(int $id, EntityManagerInterface $em): Response
    {
        // Vérifier si l'utilisateur est connecté
        $user = $this->security->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour effectuer un paiement.');
            return $this->redirectToRoute('app_login');
        }
        
        // Récupérer la formation manuellement
        $formation = $em->getRepository(Formation::class)->find($id);
        
        if (!$formation) {
            $this->addFlash('error', 'Formation non trouvée.');
            return $this->redirectToRoute('app_front_formation_index');
        }
        
        return $this->render('frontoffice/payment/form.html.twig', [
            'formation' => $formation,
            'stripe_public_key' => $_ENV['STRIPE_PUBLIC_KEY']
        ]);
    }

    #[Route('/payment/process/{id}', name: 'payment_process', methods: ['POST'])]
    public function processPayment(Request $request, int $id, EntityManagerInterface $em): JsonResponse
    {
        // Vérifier si l'utilisateur est connecté
        $user = $this->security->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Utilisateur non connecté'], 401);
        }

        // Récupérer la formation
        $formation = $em->getRepository(Formation::class)->find($id);
        if (!$formation) {
            return new JsonResponse(['error' => 'Formation non trouvée'], 404);
        }

        // Récupérer les données du formulaire
        $data = json_decode($request->getContent(), true);
        $token = $data['token'] ?? null;
        $email = $user->getEmail(); // Utiliser l'email de l'utilisateur connecté

        if (!$token) {
            return new JsonResponse(['error' => 'Token de carte manquant'], 400);
        }

        // Configurer Stripe avec la clé secrète
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        try {
            // Créer un client avec la carte
            $customer = \Stripe\Customer::create([
                'email' => $email,
                'name' => $user->getFirstName() . ' ' . $user->getLastName(),
                'source' => $token,
                'metadata' => [
                    'user_id' => $user->getId()
                ]
            ]);
            
            // Créer une charge avec le client
            $charge = \Stripe\Charge::create([
                'amount' => $formation->getPrix() * 100,
                'currency' => 'eur',
                'customer' => $customer->id,
                'description' => 'Paiement pour la formation: ' . $formation->getName(),
                'receipt_email' => $email,
                'metadata' => [
                    'formation_id' => $formation->getId(),
                    'user_id' => $user->getId()
                ]
            ]);

            // Pas d'enregistrement en base de données pour l'instant
            // Vous pourrez l'ajouter plus tard

            return new JsonResponse([
                'success' => true,
                'message' => 'Paiement effectué avec succès',
                'redirect' => $this->generateUrl('payment_success')
            ]);
        } catch (CardException $e) {
            // Erreur liée à la carte
            return new JsonResponse([
                'error' => $e->getMessage()
            ], 400);
        } catch (ApiErrorException $e) {
            // Autres erreurs Stripe
            return new JsonResponse([
                'error' => 'Une erreur est survenue lors du traitement du paiement: ' . $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            // Erreurs générales
            return new JsonResponse([
                'error' => 'Une erreur inattendue est survenue: ' . $e->getMessage()
            ], 500);
        }
    }

    #[Route('/payment/success', name: 'payment_success', methods: ['GET'])]
    public function paymentSuccess(RouterInterface $router): Response
    {
        // Vérifier si la route existe
        $formationsRoute = 'app_front_formation_index';
        if (!$router->getRouteCollection()->get($formationsRoute)) {
            // Fallback à une autre route si elle existe
            $formationsRoute = 'app_formation_index';
        }
        
        return $this->render('frontoffice/payment/success.html.twig', [
            'formations_route' => $formationsRoute
        ]);
    }

    #[Route('/payment/error', name: 'payment_error', methods: ['GET'])]
    public function paymentError(): Response
    {
        return $this->render('frontoffice/payment/error.html.twig');
    }

    #[Route('/payment/debug', name: 'payment_debug')]
    public function debugFormations(EntityManagerInterface $em): Response
    {
        $formations = $em->getRepository(Formation::class)->findAll();
        
        return $this->render('frontoffice/payment/debug.html.twig', [
            'formations' => $formations
        ]);
    }
}












