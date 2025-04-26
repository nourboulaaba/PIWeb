<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class EmailVerifier
{
    public function __construct(
        private VerifyEmailHelperInterface $verifyEmailHelper,
        private MailerInterface $mailer,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function sendEmailConfirmation(string $verifyEmailRouteName, User $user, TemplatedEmail $email): void
    {
        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            $verifyEmailRouteName,
            (string) $user->getId(),
            (string) $user->getEmail()
        );

        $context = $email->getContext();
        $context['signedUrl'] = $signatureComponents->getSignedUrl();
        $context['expiresAtMessageKey'] = $signatureComponents->getExpirationMessageKey();
        $context['expiresAtMessageData'] = $signatureComponents->getExpirationMessageData();

        $email->context($context);

        $this->mailer->send($email);
    }

    /**
     * @throws VerifyEmailExceptionInterface
     */
    public function handleEmailConfirmation(Request $request, User $user): void
    {
        // Valider la signature du lien de vérification
        $this->verifyEmailHelper->validateEmailConfirmationFromRequest($request, (string) $user->getId(), (string) $user->getEmail());

        // Vérifier l'état actuel de l'utilisateur (pour journalisation si nécessaire)
        // $isCurrentlyVerified = $user->isVerified();

        // Même si l'utilisateur est déjà vérifié, on force la mise à jour
        // pour s'assurer que le statut est correctement enregistré

        // Marquer l'utilisateur comme vérifié
        $user->setIsVerified(true);

        // S'assurer que l'utilisateur a un rôle défini
        if (empty($user->getRole())) {
            $user->setRole('EMPLOYE');
        }

        // Persister les changements
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Forcer le rafraîchissement de l'entité depuis la base de données
        $this->entityManager->clear();
        $refreshedUser = $this->entityManager->getRepository(User::class)->find($user->getId());

        // Vérifier que les changements ont été appliqués
        if (!$refreshedUser || !$refreshedUser->isVerified()) {
            throw new \RuntimeException('Impossible de marquer l\'utilisateur comme vérifié.');
        }
    }
}
