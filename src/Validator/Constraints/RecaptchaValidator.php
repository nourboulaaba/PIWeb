<?php

namespace App\Validator\Constraints;

use App\Service\RecaptchaService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class RecaptchaValidator extends ConstraintValidator
{
    private RecaptchaService $recaptchaService;
    private RequestStack $requestStack;

    public function __construct(RecaptchaService $recaptchaService, RequestStack $requestStack)
    {
        $this->recaptchaService = $recaptchaService;
        $this->requestStack = $requestStack;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof Recaptcha) {
            throw new UnexpectedTypeException($constraint, Recaptcha::class);
        }

        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return;
        }

        // Essayer d'abord de récupérer la valeur du champ caché (notre implémentation)
        $recaptchaResponse = $value;

        // Si vide, essayer de récupérer depuis la requête (implémentation standard de Google)
        if (empty($recaptchaResponse)) {
            $recaptchaResponse = $request->request->get('g-recaptcha-response');
        }

        // Si toujours vide, c'est une erreur
        if (empty($recaptchaResponse)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
            return;
        }

        // Vérifier si la réponse reCAPTCHA est valide
        if (!$this->recaptchaService->verify($recaptchaResponse)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
