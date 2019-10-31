<?php

namespace App\Form;

use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
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
                'mapped' => false,
                'empty_data' => '',
                'invalid_message' => "Vous n'avez pas saisi le même mot de passe",
                'required'=>false,
                'first_options' => [
                    'label' => 'Votre nouveau mot de passe',
                        'attr' => [
                        'placeholder' => "Votre nouveau mot de passe"
                    ]
                ],
                'second_options' => [
                    'label' => 'Confirmation de votre nouveau mot de passe',
                    'attr' => [
                        'placeholder' => "Confirmation de votre nouveau mot de passe"
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
                'label' => 'Prénom',
                'required'=>true,
                'attr' => [
                    'placeholder' => "Votre prénom"
                ]
            ])
            ->add('telephone', TextType::class, [
                'label' => 'Téléphone',
                'required'=>false,
                'attr' => [
                    'placeholder' => "Votre numéro de téléphone",
                    'maxlength'=>10,

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
            ->add('photo', TextType::class, [
                'label' => 'Photo de profil',
                'required' => false
            ])
            ->add('oldPassword', PasswordType::class, array(
                'mapped' => false,
                'label'=>'Saisissez votre mot de passe pour enregistrer',
                'attr'=>[
                    'placeholder' => "Votre ancien mot de passe",
                ]))

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

}
