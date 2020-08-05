<?php

namespace AcMarche\Mercredi\Sante\Form;

use AcMarche\Mercredi\Entity\Sante\SanteFiche;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SanteFicheType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'personne_urgence',
                TextareaType::class,
                [
                    'label' => 'Personne(s) en cas d\'urgence',
                    'help' => 'Nom, prénom et numéro de téléphone',
                ]
            )
            ->add(
                'medecin_nom',
                TextType::class,
                [
                    'label' => 'Nom du médecin',
                ]
            )
            ->add(
                'medecin_telephone',
                TextType::class,
                [
                    'label' => 'Téléphone du médecin',
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
                'remarque',
                TextareaType::class,
                [
                    'required' => false,
                    'label' => "D'autres remarques",
                ]
            )
            ->add(
                'questions',
                CollectionType::class,
                [
                    'entry_type' => SanteReponseType::class,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => SanteFiche::class,
            ]
        );
    }
}
