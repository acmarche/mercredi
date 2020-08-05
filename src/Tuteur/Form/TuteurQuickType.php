<?php

namespace AcMarche\Mercredi\Tuteur\Form;

use AcMarche\Mercredi\Entity\Tuteur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TuteurQuickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
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
                'rue',
                TextType::class,
                [
                    'required' => true,
                ]
            )
            ->add(
                'code_postal',
                IntegerType::class,
                [
                    'required' => true,
                ]
            )
            ->add(
                'localite',
                TextType::class,
                [
                    'required' => true,
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'required' => false,
                    'help' => 'Si une adresse mail est encodée, un compte sera créé',
                ]
            )
            ->add(
                'telephone',
                TextType::class,
                [
                    'required' => true,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Tuteur::class,
            ]
        );
    }
}
