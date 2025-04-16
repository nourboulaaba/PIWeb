<?php

namespace App\Form;
use Symfony\Component\Form\Extension\Core\Type\DateType;

use App\Entity\Contrat;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ContratType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('idEmploye', null, [
                'label' => 'ID Employé',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez l\'ID de l\'employé'
                ]
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type de contrat',
                'choices' => [
                    'CDI' => 'CDI',
                    'CDD' => 'CDD',
                    'Stage' => 'Stage',
                    'Intérim' => 'Interim',
                    'Freelance' => 'Freelance'
                ],
                'attr' => ['class' => 'form-select']
            ])
            ->add('dateDebut', DateType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control']
            ])
            ->add('dateFin', DateType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control']
            ])
            ->add('salaire', null, [
                'label' => 'Salaire',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez le montant du salaire'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Contrat::class,
        ]);
    }
}