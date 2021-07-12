<?php

namespace AcMarche\Mercredi\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;

final class MonthWidgetType extends AbstractType
{
    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'label' => 'Mois',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Format mois-année: 06-2021',
                    'autocomplete' => 'off',
                ],
                'constraints' => [new Regex('#^\d{2}-\d{4}$#')],
            ]
        );
    }

    public function getParent()
    {
        return TextType::class;
    }
}
