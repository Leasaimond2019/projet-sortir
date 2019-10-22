<?php

namespace App\Form;

use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;


class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pseudo', TextType::class, [
                'label' => 'Pseudo',
                'required'=>true,
                'attr' => [
                    'placeholder' => "Votre pseudo"
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => "Vous n'avez pas saisi le même mot de passe",
                'first_options' => [
                    'label' => 'Votre mot de passe',
                    'required'=>true,
                    'attr' => [
                        'placeholder' => "Votre mot de passe"
                    ]
                ],
                'second_options' => [
                    'label' => 'Confirmation de votre mot de passe',
                    'required'=>true,
                    'attr' => [
                        'placeholder' => "Confirmation de votre mot de passe"
                    ]
                ]
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'required'=>true,
                'attr' => [
                    'placeholder' => "Votre nom"
                ]
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prenom',
                'required'=>true,
                'attr' => [
                    'placeholder' => "Votre prénom"
                ]
            ])
            ->add('telephone', TextType::class, [
                'label' => 'Telephone',
                'attr' => [
                    'placeholder' => "Votre numéro de téléphone"
                ]
            ])
            ->add('mail', EmailType ::class, [
                'label' => 'Mail',
                'required'=>true,
                'attr' => [
                    'placeholder' => "Votre email"
                ]
            ])
            ->add('no_site', EntityType::class, [
                'class'=> 'App\Entity\Site',
                'label'=> 'Site',
                'required'=>true,
                'choice_label' => 'nom_site',
                'placeholder' => 'Choisir un site',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c');
                }
            ])


        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

}
