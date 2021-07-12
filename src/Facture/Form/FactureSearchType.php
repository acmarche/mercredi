<?php

namespace AcMarche\Mercredi\Facture\Form;

use AcMarche\Mercredi\Entity\Ecole;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;

final class FactureSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'tuteur',
                SearchType::class,
                [
                    'required' => false,
                    'attr' => ['placeholder' => 'Nom du tuteur'],
                ]
            )->add(
                'month',
                TextType::class,
                [
                    'label' => 'Mois',
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'Format mois-année: 06-2021',
                        'autocomplete' => 'off',
                    ],
                    'constraints' => [new Regex('#^\d{2}-\d{4}$#')],
                ]
            )
            ->add(
                'ecole',
                EntityType::class,
                [
                    'class' => Ecole::class,
                    'required' => false,
                    'placeholder' => 'Choisissez une école',
                    'attr' => ['class' => 'custom-select my-1 mr-sm-2'],
                ]
            )
            ->add(
                'paye',
                ChoiceType::class,
                [
                    'label' => 'Payé',
                    'placeholder' => 'Payé ou non',
                    'choices' => ['Payée' => 1, 'Non payée' => 0],
                    'required' => false,
                ]
            );
    }
}
