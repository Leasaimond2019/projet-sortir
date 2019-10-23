<?php

namespace App\Form;

use App\Entity\Lieu;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LieuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom_lieu')
            ->add('rue')
            ->add('latitude')
            ->add('longitude')
            ->add('no_ville', EntityType::class, [
                'class' => 'App\Entity\Ville',
                'choice_label' => 'nom_ville',
                'label' => 'Ville',
                'placeholder' => 'Choisir une ville',
                'query_builder' => function(EntityRepository $er) {
                    return $er -> createQueryBuilder('l');
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }
}
