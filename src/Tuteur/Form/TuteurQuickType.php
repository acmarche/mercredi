<?php

namespace AcMarche\Mercredi\Tuteur\Form;

use AcMarche\Mercredi\Entity\Tuteur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TuteurQuickType extends AbstractType
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
                    self::REQUIRED => true,
                ]
            )
            ->add(
                'code_postal',
                IntegerType::class,
                [
                    self::REQUIRED => true,
                ]
            )
            ->add(
                'localite',
                TextType::class,
                [
                    self::REQUIRED => true,
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    self::REQUIRED => false,
                    'help' => 'Si une adresse mail est encodée, un compte sera créé',
                ]
            )
            ->add(
                'telephone',
                TextType::class,
                [
                    self::REQUIRED => true,
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => Tuteur::class,
            ]
        );
    }
}
