<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;


class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isEdit = $options['is_edit'];

        $builder
            ->add('email', EmailType::class, [
                'constraints' => [
                    
            
                ]
            ])
            ->add('first_name', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [
                    new NotBlank(['message' => 'Le prénom est requis.']),
                    new Length(['min' => 2, 'minMessage' => 'Le prénom doit contenir au moins {{ limit }} caractères.']),
                ]
            ])
            ->add('last_name', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new NotBlank(['message' => 'Le nom est requis.']),
                    new Length(['min' => 2, 'minMessage' => 'Le nom doit contenir au moins {{ limit }} caractères.']),
                ]
            ]);

        // Ajouter le mot de passe uniquement si on N'EST PAS en édition
        if (!$isEdit) {
            $builder->add('password', PasswordType::class, [
                'mapped' => true,
                'required' => true,
                'constraints' => [
                
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères.',
                        'max' => 4096,
                    ]),
                ],
            ]);
        }

        $builder
            ->add('cin', TextType::class, [
                'label' => 'CIN',
                'required' => false,
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[0-9]{8}$/',
                        'message' => 'Le CIN doit contenir exactement 8 chiffres.'
                    ]),
                ]
            ])
            ->add('phone_number', TextType::class, [
                'label' => 'Numéro de téléphone',
                'required' => false,
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[0-9]{8}$/',
                        'message' => 'Le numéro doit contenir exactement 8 chiffres.'
                    ]),
                ]
            ])
            ->add('cv', FileType::class, [
                'label' => 'CV (fichier PDF)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => ['application/pdf'],
                        'mimeTypesMessage' => 'Veuillez uploader un fichier PDF valide.',
                    ]),
                ]
            ])
            ->add('profile_photo', FileType::class, [
                'label' => 'Photo de profil',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => ['image/jpeg', 'image/png'],
                        'mimeTypesMessage' => 'Veuillez uploader une image JPEG ou PNG.',
                    ]),
                ]
            ])
            ->add('salary', NumberType::class, [
                'required' => false
            ])
            ->add('role', ChoiceType::class, [
                'choices' => [
                    'Candidat' => 'CANDIDAT',
                    'Employé' => 'EMPLOYE',
                    'RH' => 'RH',
                ],
                'expanded' => true,
                'multiple' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez choisir un rôle.'])
                ]
            ]);
            $builder
    ->add('isVerified', CheckboxType::class, [
        'label' => 'Compte activé',
        'required' => false,
    ])
    // autres champs...
;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'is_edit' => false,
        ]);
    }
}
