<?php

namespace App\Form;

use App\Entity\Sortie;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la sortie'
            ])
            ->add('date_debut', TextType::class, [
                'label' => 'Date et heure de début'
            ])
            ->add('duree', TextType::class, [
                'label' => 'Durée de la sortie'
            ])
            ->add('date_cloture', TextType::class, [
                'label' => 'Date et heure de fin'
            ])
            ->add('nb_inscription_max', TextType::class, [
                'label' => 'Nombre maximum de participants'
            ])
            ->add('description', TextType::class, [
                'label' => 'Description'
            ])
            ->add('url_photo', TextType::class, [
                'label' => 'Photo de présentation'
            ])
            ->add('no_lieu', EntityType::class, [
                'class' => 'App\Entity\Lieu',
                'choice_label' => 'Lieu',
                'placeholder' => 'Choisir un lieu',
                'query_builder' => function(EntityRepository $er) {
                return $er -> createQueryBuilder('l');
                }
            ])
            ->add('no_site', EntityType::class, [
                'class' => 'App\Entity\Site',
                'choice_label' => 'Site',
                'placeholder' => 'Choisir un site',
                'query_builder' => function(EntityRepository $er) {
                    return $er -> createQueryBuilder('s');
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
