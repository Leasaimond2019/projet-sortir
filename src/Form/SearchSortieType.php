<?php

namespace App\Form;

use App\Entity\SearchSortie;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('site', EntityType::class, [
                'class' => 'App\Entity\Site',
                'choice_label' => 'nom_site',
                'placeholder' => 'Choisir un site organisateur',
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('s');
                },
                'required' => false
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'required' => false
            ])
            ->add('date_debut', DateType::class, [
                'label' => "Date de début",
                'widget' => 'single_text',
                'required' => false
            ])
            ->add('date_fin', DateType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text',
                'required' => false
            ])
            ->add('chk_organisateur', CheckboxType::class, [
                'label' => "Sorties dont je suis l'organisateur/trice",
                'required' => false
            ])
            ->add('chk_inscrit', CheckboxType::class, [
                "label" => "Sorties auxquelles je suis inscrit/e",
                'required' => false
            ])
            ->add('chk_non_inscrit', CheckboxType::class, [
                "label" => "Sorties auxquelles je ne suis pas inscrite",
                'required' => false
            ])
            ->add('chk_passe', CheckboxType::class, [
                "label" => "Sorties passées",
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SearchSortie::class,
        ]);
    }
}
