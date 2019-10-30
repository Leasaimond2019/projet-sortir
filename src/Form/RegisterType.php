<?php

namespace App\Form;

use App\Entity\User;
use Doctrine\DBAL\Types\BooleanType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pseudo', TextType::class, [
                'label' => 'Pseudo',
                'required'=>true,
                'attr' => [
                    'placeholder' => "Pseudo"
                ]
            ])
            //->add('roles')
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => "Vous n'avez pas saisi le même mot de passe",
                'first_options' => [
                    'label' => 'Mot de passe',
                    'required'=>true,
                    'attr' => [
                        'placeholder' => "Mot de passe"
                    ]
                ],
                'second_options' => [
                    'label' => 'Confirmation du mot de passe',
                    'required'=>true,
                    'attr' => [
                        'placeholder' => "Confirmation du mot de passe"
                    ]
                ]
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'required'=>true,
                'attr' => [
                    'placeholder' => "Nom"
                ]
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'required'=>true,
                'attr' => [
                    'placeholder' => "Prénom"
                ]
            ])
            ->add('telephone', TextType::class, [
                'label' => 'Téléphone',
                'required'=>false,
                'attr' => [
                    'placeholder' => "Numéro de téléphone"
                ]
            ])
            ->add('mail', EmailType ::class, [
                'label' => 'Mail',
                'required'=>true,
                'attr' => [
                    'placeholder' => "Email"
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
