<?php

namespace App\Controller;

use App\Entity\User;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Attribute\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, EntityManagerInterface $entityManager): Response
    {
        $id = $request->query->get('id');

        if (null === $id) {
            return $this->redirectToRoute('app_login');
        }

        $user = $entityManager->getRepository(User::class)->find($id);

        if (null === $user) {
            return $this->redirectToRoute('app_login');
        }

        // Validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);

            // Forcer la mise à jour du statut de vérification
            $user->setIsVerified(true);

            // S'assurer que l'utilisateur a un rôle défini
            if (empty($user->getRole())) {
                $user->setRole('EMPLOYE');
            }

            // Persister les changements
            $entityManager->persist($user);
            $entityManager->flush();

            // Vérifier que les changements ont été appliqués
            $entityManager->refresh($user);
            if (!$user->isVerified()) {
                throw new \RuntimeException('Impossible de marquer l\'utilisateur comme vérifié.');
            }
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_login');
        }

        $this->addFlash('success', '<strong>Félicitations !</strong> Votre adresse email a été vérifiée avec succès. <br><br>Votre compte est maintenant en attente d\'activation par un administrateur. <br>Vous recevrez une notification lorsque votre compte sera activé.');

        return $this->redirectToRoute('app_login');
    }

    #[Route('/verify/resend', name: 'app_verify_resend_email')]
    public function resendVerifyEmail(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user || !$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        // Vérifier si l'utilisateur est déjà vérifié
        if ($user->isVerified()) {
            $this->addFlash('info', 'Votre adresse email est déjà vérifiée. Vous pouvez vous connecter.');
            return $this->redirectToRoute('app_login');
        }

        // Rafraîchir l'utilisateur depuis la base de données pour s'assurer d'avoir les données les plus récentes
        $entityManager->refresh($user);

        // Envoyer l'email de vérification
        $this->sendVerificationEmail($user);

        $this->addFlash('success', '<strong>Email envoyé !</strong> <br><br>Un nouveau lien de vérification a été envoyé à <strong>' . $user->getEmail() . '</strong>. <br><br>Veuillez vérifier votre boîte de réception et cliquer sur le lien pour vérifier votre adresse email.');

        return $this->redirectToRoute('app_login');
    }

    private function sendVerificationEmail(User $user): void
    {
        $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            (new TemplatedEmail())
                ->from(new Address('boulaabanour2020@gmail.com', 'TrueMatch'))
                ->to($user->getEmail())
                ->subject('Confirmation de votre adresse email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );
    }
}
