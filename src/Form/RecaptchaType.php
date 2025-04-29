<?php

namespace App\Form;

use App\Service\RecaptchaService;
use App\Validator\Constraints\Recaptcha;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class RecaptchaType extends AbstractType
{
    private RecaptchaService $recaptchaService;

    public function __construct(RecaptchaService $recaptchaService)
    {
        $this->recaptchaService = $recaptchaService;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'mapped' => false,
            'required' => true,
            'error_bubbling' => false,
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez cocher la case reCAPTCHA.'
                ]),
                new Recaptcha()
            ],
            'attr' => [
                'class' => 'g-recaptcha',
                'data-sitekey' => $this->recaptchaService->getSiteKey()
            ]
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        // Assurez-vous que le type est hidden pour le rendu HTML
        $view->vars['type'] = 'hidden';
        $view->vars['attr'] = array_merge($view->vars['attr'], [
            'data-sitekey' => $this->recaptchaService->getSiteKey()
        ]);
    }

    public function getParent()
    {
        return HiddenType::class;
    }

    public function getBlockPrefix()
    {
        return 'recaptcha';
    }
}
