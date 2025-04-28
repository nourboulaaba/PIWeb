<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReclamationFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('classification', ChoiceType::class, [
                'choices' => [
                    'Tous' => null,
                    'Urgente' => 'Cette réclamation est à classer comme **Urgente**.',
                    'Moyenne' => 'Cette réclamation est à classer comme **Moyenne**.',
                    'Simple' => 'Cette réclamation est à classer comme **Simple**.',
                ],
                'required' => false,
                'placeholder' => 'Choisir la classification',  // Placeholder
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // You can add other options if needed
        ]);
    }
}
