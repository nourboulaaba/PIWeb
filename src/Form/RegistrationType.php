<?php
namespace App\Form;

use App\Entity\User;
use App\Form\RecaptchaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Email;


class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isEdit = $options['is_edit'];  // récupération de l'option

        $builder
            ->add('first_name', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [
                    new NotBlank(['message' => 'Le prénom est requis.']),
                    new Length(['min' => 2, 'minMessage' => 'Le prénom doit contenir au moins {{ limit }} caractères.']),
                ],
            ])
            ->add('last_name', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new NotBlank(['message' => 'Le nom est requis.']),
                    new Length(['min' => 2, 'minMessage' => 'Le nom doit contenir au moins {{ limit }} caractères.']),
                ],
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                   // new NotBlank(['message' => 'L\'email est requis.']),
                    new Email(['message' => 'L\'adresse email est invalide.']),
                ],
            ]);

        if (!$isEdit) {  // si c'est un formulaire d'inscription
            $builder ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent correspondre.',
                'first_options'  => [
                    'label' => 'Mot de passe',
                    'attr' => ['class' => 'password-field form-control'],
                    'constraints' => [
                        //new NotBlank(['message' => 'Le mot de passe est requis.']),
                        new Length(['min' => 6, 'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères.']),
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirmer le mot de passe',
                    'attr' => ['class' => 'password-field form-control'],
                ],

            ])
            //->add('recaptcha', RecaptchaType::class)
            ;
        }


        // Si tu veux gérer aussi le CV ou d’autres champs
        // tu pourrais ajouter ici : if ($isEdit) { $builder->add('cv', FileType::class, [...]); }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'is_edit' => false, // déclaration propre de l'option pour éviter ton erreur
        ]);
    }
}
