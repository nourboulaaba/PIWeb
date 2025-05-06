<?php

namespace App\Form;

use App\Entity\Offre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OffreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('titre', TextType::class, [
                'attr' => ['class' => 'form-control'], // Add Bootstrap class
            ])
            ->add('description', TextType::class, [
                'attr' => ['class' => 'form-control'], // Add Bootstrap class
            ])
            ->add('salaireMin', NumberType::class, [
                'attr' => ['class' => 'form-control'], // Add Bootstrap class
            ])
            ->add('salaireMax', NumberType::class, [
                'attr' => ['class' => 'form-control'], // Add Bootstrap class
            ])
            ->add('departement')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Offre::class,
        ]);
    }
}
