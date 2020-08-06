<?php

namespace AcMarche\Mercredi\Sante\Form;

use AcMarche\Mercredi\Entity\Sante\SanteFiche;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SanteFicheType extends AbstractType
{
    /**
     * @var string
     */
    private const LABEL = 'label';

    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'personne_urgence',
                TextareaType::class,
                [
                    self::LABEL => 'Personne(s) en cas d\'urgence',
                    'help' => 'Nom, prénom et numéro de téléphone',
                ]
            )
            ->add(
                'medecin_nom',
                TextType::class,
                [
                    self::LABEL => 'Nom du médecin',
                ]
            )
            ->add(
                'medecin_telephone',
                TextType::class,
                [
                    self::LABEL => 'Téléphone du médecin',
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
                    self::LABEL => "D'autres remarques",
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

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => SanteFiche::class,
            ]
        );
    }
}
