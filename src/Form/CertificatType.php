<?php

namespace App\Form;

use App\Entity\Certificat;
use App\Entity\Formation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CertificatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('formation', EntityType::class, [
                'class' => Formation::class,
                'choice_label' => 'name',
                'placeholder' => 'Sélectionnez une formation',
                'attr' => [
                    'class' => 'form-select select2',
                ],
                'label' => 'Formation associée',
                'help' => 'Choisissez la formation pour laquelle ce certificat est délivré'
            ])
            ->add('dateExamen', null, [
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control datepicker',
                ],
                'label' => 'Date d\'examen',
                'help' => 'Date à laquelle l\'examen de certification aura lieu'
            ])
            ->add('heure', null, [
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control timepicker',
                ],
                'label' => 'Heure de l\'examen',
                'help' => 'Heure de début de l\'examen'
            ])
            ->add('duree', null, [
                'attr' => [
                    'min' => 30,
                    'max' => 240,
                    'step' => 15,
                    'placeholder' => 'Durée en minutes'
                ],
                'label' => 'Durée (minutes)',
                'help' => 'Durée de l\'examen en minutes'
            ])
            ->add('niveau', ChoiceType::class, [
                'choices' => [
                    'Débutant' => 'Débutant',
                    'Intermédiaire' => 'Intermédiaire',
                    'Avancé' => 'Avancé',
                    'Expert' => 'Expert',
                ],
                'placeholder' => 'Niveau de certification',
                'required' => true,
                'attr' => [
                    'class' => 'form-select',
                ],
                'label' => 'Niveau',
                'help' => 'Niveau de difficulté de la certification'
            ])
            ->add('prixExam', null, [
                'attr' => [
                    'placeholder' => 'Prix en DT',
                    'min' => 0
                ],
                'label' => 'Prix de l\'examen (DT)',
                'help' => 'Coût de l\'examen de certification'
            ])
            ->add('resultatExamen', ChoiceType::class, [
                'choices' => [
                    'En attente' => null,
                    'Réussi' => 'Réussi',
                    'Échec' => 'Échec',
                ],
                'placeholder' => 'Résultat de l\'examen',
                'required' => false,
                'attr' => [
                    'class' => 'form-select',
                ],
                'label' => 'Résultat',
                'help' => 'Résultat de l\'examen (laissez vide si l\'examen n\'a pas encore eu lieu)'
            ])
            ->add('dateReprogrammation', null, [
                'widget' => 'single_text',
                'required' => false,
                'attr' => [
                    'class' => 'form-control datepicker',
                ],
                'label' => 'Date de reprogrammation',
                'help' => 'Date de reprogrammation en cas d\'échec (optionnel)'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Certificat::class,
        ]);
    }
}
