<?php

namespace App\Form;

use App\Entity\Conge;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints as Assert;

class CongeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('employe', EntityType::class, [
                'class' => User::class,
                'choice_label' => function (User $user) {
                    return $user->getId();  // Affiche l'ID de l'utilisateur
                },
                'placeholder' => 'Choisir un employé',
                'label' => 'Employé',
            ])
            ->add('dateDebut', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de début',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'La date de début ne peut pas être vide.',
                    ]),
                    new Assert\Type([
                        'type' => '\DateTimeInterface',
                        'message' => 'La date de début doit être une date valide.',
                    ]),
                ],
            ])
            ->add('dateFin', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de fin',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'La date de fin ne peut pas être vide.',
                    ]),
                    new Assert\Type([
                        'type' => '\DateTimeInterface',
                        'message' => 'La date de fin doit être une date valide.',
                    ]),
                    new Assert\GreaterThanOrEqual([
                        'propertyPath' => 'parent.all[dateDebut].data',
                        'message' => 'La date de fin doit être supérieure ou égale à la date de début.',
                    ]),
                ],
            ])
            ->add('typeConge', ChoiceType::class, [
                'choices' => [
                    'Maladie' => 'maladie',
                    'Annuelle' => 'annuelle',
                    'Maternité' => 'maternité',
                    'Sans Solde' => 'sans solde',
                ],
                'placeholder' => 'Choisir un type',
                'label' => 'Type de congé',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Le type de congé ne peut pas être vide.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Conge::class,
        ]);
    }
}
