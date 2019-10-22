<?php

namespace App\Form;

use App\Entity\Sortie;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
            ->add('date_debut', DateTimeType::class, [
                'label' => 'Date et heure de début',
                'widget' => 'single_text'
            ])
            ->add('duree', IntegerType::class, [
                'label' => 'Durée de la sortie'
            ])
            ->add('nb_inscription_max', IntegerType::class, [
                'label' => 'Nombre maximum de participants'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description'
            ])
            ->add('url_photo', TextType::class, [
                'label' => 'Photo de présentation'
            ])
            ->add('no_lieu', EntityType::class, [
                'class' => 'App\Entity\Lieu',
                'choice_label' => 'nom_lieu',
                'label' => 'Lieu',
                'placeholder' => 'Choisir un lieu',
                'query_builder' => function(EntityRepository $er) {
                    return $er -> createQueryBuilder('l');
                }
            ])
            ->add('no_site', EntityType::class, [
                'class' => 'App\Entity\Site',
                'choice_label' => 'nom_site',
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
