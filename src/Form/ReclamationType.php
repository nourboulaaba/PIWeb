<?php

namespace App\Form;

use App\Entity\Reclamation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

class ReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sujet', TextType::class, [
                'label' => 'Sujet de la réclamation',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez le sujet de votre réclamation'
                ],
                'required' => true
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description détaillée',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 5,
                    'placeholder' => 'Décrivez votre problème en détails...'
                ],
                'required' => true
            ])
            ->add('imagePath', FileType::class, [
                'label' => 'Joindre une image (optionnel)',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control-file',
                    'accept' => 'image/*'
                ]
            ])
            ->add('date', DateType::class, [
                'label' => 'Date de la réclamation',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control datepicker'
                ],
                'data' => new \DateTime(),
                'required' => true
            ])
            ->add('userId', IntegerType::class, [
                'label' => 'Votre ID utilisateur',
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Veuillez saisir votre ID utilisateur.']),
                    new Assert\Positive(['message' => 'L\'ID doit être un nombre positif.']),
                ],
                'attr' => ['class' => 'form-control', 'min' => 1]
            ])
            ->add('statut', ChoiceType::class, [
                'choices' => [
                    'Non traité' => 'Non traité',
                    'En cours' => 'En cours',
                    'Traité' => 'Traité',
                ],
                'label' => 'Statut de la réclamation',
                'attr' => ['class' => 'form-control'],
            ]);;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
        ]);
    }
}
