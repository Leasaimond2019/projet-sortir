<?php

namespace App\Form;

use App\Entity\Sortie;
use Doctrine\ORM\EntityManager;
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
            ]);
            if($options['modification']==true){
            $builder->add('no_site',EntityType::class,[
                'class' => 'App\Entity\Site',
                'choice_label' => 'nom_site',
                'label' => 'Site',
                'placeholder' => 'Choisir un site',
                'query_builder' => function(EntityRepository $er) {
                    return $er -> createQueryBuilder('l');
                },
            ]);}
            $builder->add('date_debut', DateTimeType::class, [
                'label' => 'Date et heure de début',
                'widget' => 'single_text'
            ])
            ->add('duree', IntegerType::class, [
                'label' => 'Durée de la sortie',
                'attr' => [
                'step' => 5
                ]
            ])
            ->add('date_cloture', DateTimeType::class, [
                'label' => 'Date et heure limite d\'inscription',
                'widget' => 'single_text'
            ])
            ->add('nb_inscription_max', IntegerType::class, [
                'label' => 'Nombre maximum de participants'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description'
            ])
            ->add('url_photo', TextType::class, [
                'label' => 'Photo de présentation',
                'required' => false
            ])
            ->add('no_lieu', EntityType::class, [
                'class' => 'App\Entity\Lieu',
                'choice_label' => 'nom_lieu',
                'label' => 'Lieu',
                'placeholder' => 'Choisir un lieu',
                'query_builder' => function(EntityRepository $er) {
                    return $er -> createQueryBuilder('l');
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
            'modification'=>null,

        ]);
    }
}
