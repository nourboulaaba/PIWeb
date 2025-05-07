<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormationSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('keyword', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Rechercher une formation...',
                    'class' => 'form-control'
                ]
            ])
            ->add('prix_min', NumberType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Prix min',
                    'class' => 'form-control'
                ]
            ])
            ->add('prix_max', NumberType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Prix max',
                    'class' => 'form-control'
                ]
            ])
            ->add('sort_by', ChoiceType::class, [
                'label' => false,
                'required' => false,
                'choices' => [
                    'Trier par' => '',
                    'Prix (croissant)' => 'prix_asc',
                    'Prix (décroissant)' => 'prix_desc',
                    'Date (récente)' => 'date_desc',
                    'Date (ancienne)' => 'date_asc'
                ],
                'attr' => [
                    'class' => 'form-select'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}


