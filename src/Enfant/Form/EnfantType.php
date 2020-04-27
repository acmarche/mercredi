<?php

namespace AcMarche\Mercredi\Enfant\Form;

use AcMarche\Mercredi\Data\MercrediConstantes;
use AcMarche\Mercredi\Entity\Ecole;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Form\Type\OrdreType;
use AcMarche\Mercredi\Form\Type\RemarqueType;
use AcMarche\Mercredi\Scolaire\ScolaireData;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnfantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'nom',
                TextType::class,
                [
                    'required' => true,
                ]
            )
            ->add(
                'prenom',
                TextType::class,
                [
                    'required' => true,
                ]
            )
            ->add(
                'birthday',
                BirthdayType::class,
                [
                    'label' => 'Né le',
                    'widget' => 'single_text',
                    'required' => false,
                ]
            )
            ->add(
                'numero_national',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'sexe',
                ChoiceType::class,
                [
                    'required' => false,
                    'choices' => MercrediConstantes::SEXES,
                    'placeholder' => 'Choisissez son sexe',
                ]
            )
            ->add(
                'ordre',
                OrdreType::class,
                [

                ]
            )
            ->add(
                'ecole',
                EntityType::class,
                [
                    'class' => Ecole::class,
                    'required' => false,
                    'placeholder' => 'Choisissez son école',
                ]
            )
            ->add(
                'annee_scolaire',
                ChoiceType::class,
                [
                    'choices' => array_combine(ScolaireData::ANNEES_SCOLAIRES, ScolaireData::ANNEES_SCOLAIRES),
                    'label' => 'Année scolaire',
                    'placeholder' => 'Choisissez son année scolaire',
                ]
            )
            ->add(
                'groupe_scolaire',
                ChoiceType::class,
                [
                    'choices' => ScolaireData::GROUPES_SCOLAIRES,
                    'required' => false,
                    'label' => 'Forcer le groupe scolaire',
                    'placeholder' => 'Choisissez un groupe',
                    'help' => 'Utilisé pour le listing des présences',
                ]
            )
            ->add(
                'remarque',
                RemarqueType::class
            )
            ->add(
                'photoAutorisation',
                CheckboxType::class,
                [
                    'required' => false,
                    'label' => 'Autorisation de ses photos',
                    'help' => 'Cochez si les parents autorisent la diffusion des photos de l\'enfant',
                ]
            )
            ->add(
                'accompagnateurs',
                CollectionType::class,
                [
                    'entry_type' => TextType::class,
                    'entry_options' => [],
                    'prototype' => true,
                    'required' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                ]
            )
            ->add(
                'photo',
                VichImageType::class,
                [
                    'required' => false,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Enfant::class,
            ]
        );
    }
}
