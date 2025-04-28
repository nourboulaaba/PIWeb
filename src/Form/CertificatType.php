<?php

namespace App\Form;

use App\Entity\Certificat;
use App\Entity\Formation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CertificatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
{
    $builder
        ->add('nom')
        ->add('email')
        ->add('dateExamen', null, [
            'widget' => 'single_text',
        ])
        ->add('heure', null, [
            'widget' => 'single_text',
        ])
        ->add('duree')
        ->add('prixExam')
        ->add('niveau')
        ->add('resultatExamen')
        ->add('dateReprogrammation', null, [
            'widget' => 'single_text',
        ])
        ->add('formation', EntityType::class, [
            'class' => Formation::class,
            'choice_label' => 'id',
        ]);
}


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Certificat::class,
        ]);
    }
}
