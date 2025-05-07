<?php

namespace App\Form;

use App\Entity\Recrutement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Offre;

class RecrutementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('dateDebut', DateType::class, [
                'widget' => 'single_text',      // This removes the dropdowns
                'html5' => false,               // Prevents native datepicker
                'attr' => [
                    'class' => 'form-control flatpickr-date'
                ]
            ])
            ->add('dateFin', DateType::class, [
                'widget' => 'single_text',      // This removes the dropdowns
                'html5' => false,               // Prevents native datepicker
                'attr' => [
                    'class' => 'form-control flatpickr-date'
                ]
            ])

            ->add('NbEntretien')
            //->add('offre')
            ->add('offre', EntityType::class, [
                'class' => Offre::class,
                'choice_label' => 'titre',
                'multiple' => false,  // Pas de sélection multiple
                'expanded' => false,  // Liste déroulante (dropdown)
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recrutement::class,
        ]);
    }
}
