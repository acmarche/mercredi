<?php

namespace AcMarche\Mercredi\Facture\Form;

use AcMarche\Mercredi\Entity\Facture\FactureDecompte;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class FactureDecompteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'payeLe',
                DateType::class,
                [
                    'label' => 'Date de paiement',
                    'widget' => 'single_text',
                    'required' => true,
                    'attr' => [
                        'autocomplete' => 'off',
                    ],
                ]
            )
            ->add(
                'remarque',
                TextType::class,
                [
                    'required' => false,
                    'label' => 'Remarque',
                ]
            )
            ->add(
                'montant',
                MoneyType::class,
                [
                    'required' => true,
                    'help' => 'Montant payé, uniquement les chiffres',
                    'constraints' => [
                        new Assert\GreaterThan([
                            'value' => 0,
                        ])
                    ],
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => FactureDecompte::class,
            ]
        );
    }
}
