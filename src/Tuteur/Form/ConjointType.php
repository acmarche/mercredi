<?php

namespace AcMarche\Mercredi\Tuteur\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ConjointType extends AbstractType
{
    /**
     * @var string
     */
    private const LABEL = 'label';
    /**
     * @var string
     */
    private const REQUIRED = 'required';
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'relation_conjoint',
                TextType::class,
                [
                    self::LABEL => 'Relation entre les conjoints',
                    self::REQUIRED => false,
                    'help' => 'Papa, Belle-mère, Maman, Oncle...',
                ]
            )
            ->add(
                'nom_conjoint',
                TextType::class,
                [
                    self::LABEL => 'Nom',
                    self::REQUIRED => false,
                ]
            )
            ->add(
                'prenom_conjoint',
                TextType::class,
                [
                    self::LABEL => 'Prénom',
                    self::REQUIRED => false,
                ]
            )
            ->add(
                'email_conjoint',
                EmailType::class,
                [
                    self::REQUIRED => false,
                    self::LABEL => 'Email',
                ]
            )
            ->add(
                'telephone_conjoint',
                TextType::class,
                [
                    self::REQUIRED => false,
                    self::LABEL => 'Téléphone',
                ]
            )
            ->add(
                'telephone_bureau_conjoint',
                TextType::class,
                [
                    self::REQUIRED => false,
                    self::LABEL => 'Téléphone du bureau',
                ]
            )
            ->add(
                'gsm_conjoint',
                TextType::class,
                [
                    self::LABEL => 'Gsm',
                    self::REQUIRED => false,
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'inherit_data' => true,
            ]
        );
    }
}
