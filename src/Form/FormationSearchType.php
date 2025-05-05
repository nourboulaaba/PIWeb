<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormationSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('keyword', TextType::class, [
                'label' => 'Mot-clé',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Rechercher par nom ou description',
                    'class' => 'form-control'
                ]
            ])
            ->add('prix_min', NumberType::class, [
                'label' => 'Prix minimum',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Prix min',
                    'class' => 'form-control'
                ]
            ])
            ->add('prix_max', NumberType::class, [
                'label' => 'Prix maximum',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Prix max',
                    'class' => 'form-control'
                ]
            ])
            ->add('date_debut', DateType::class, [
                'label' => 'Date de début',
                'required' => false,
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('date_fin', DateType::class, [
                'label' => 'Date de fin',
                'required' => false,
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('sort_by', ChoiceType::class, [
                'label' => 'Trier par',
                'required' => false,
                'choices' => [
                    'Nom (A-Z)' => 'name_asc',
                    'Nom (Z-A)' => 'name_desc',
                    'Prix (croissant)' => 'prix_asc',
                    'Prix (décroissant)' => 'prix_desc',
                    'Date (récente)' => 'date_desc',
                    'Date (ancienne)' => 'date_asc',
                    'Popularité' => 'popularity'
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
