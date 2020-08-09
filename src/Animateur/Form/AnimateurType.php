<?php

namespace AcMarche\Mercredi\Animateur\Form;

use AcMarche\Mercredi\Data\MercrediConstantes;
use AcMarche\Mercredi\Entity\Animateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AnimateurType extends AbstractType
{
    /**
     * @var string
     */
    private const REQUIRED = 'required';

    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'nom',
                TextType::class,
                [
                    self::REQUIRED => true,
                ]
            )
            ->add(
                'prenom',
                TextType::class,
                [
                    self::REQUIRED => true,
                ]
            )
            ->add(
                'rue',
                TextType::class,
                [
                    self::REQUIRED => false,
                ]
            )
            ->add(
                'code_postal',
                IntegerType::class,
                [
                    self::REQUIRED => false,
                ]
            )
            ->add(
                'localite',
                TextType::class,
                [
                    self::REQUIRED => false,
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    self::REQUIRED => false,
                ]
            )
            ->add(
                'telephone',
                TextType::class,
                [
                    self::REQUIRED => false,
                ]
            )
            ->add(
                'telephone_bureau',
                TextType::class,
                [
                    self::REQUIRED => false,
                ]
            )
            ->add(
                'gsm',
                TextType::class,
                [
                    self::REQUIRED => false,
                ]
            )
            ->add(
                'sexe',
                ChoiceType::class,
                [
                    self::REQUIRED => false,
                    'choices' => MercrediConstantes::SEXES,
                    'placeholder' => 'Choisissez son sexe',
                ]
            )
            ->add(
                'remarque',
                TextareaType::class,
                [
                    self::REQUIRED => false,
                    'attr' => ['rows' => 8],
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => Animateur::class,
            ]
        );
    }
}
