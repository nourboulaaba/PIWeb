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

        $recaptchaResponse = $request->request->get('g-recaptcha-response');
        //  dd($this->recaptchaService);
        if (!$this->recaptchaService->verify($recaptchaResponse)) {
            dd("notverifyed");
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
