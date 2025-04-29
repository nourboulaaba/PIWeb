<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Recaptcha extends Constraint
{
    public string $message = 'La vérification reCAPTCHA a échoué. Veuillez réessayer.';
}
