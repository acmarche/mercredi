<?php

namespace AcMarche\Mercredi\Search\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SearchEnfantEcoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $ecoles = [];
        foreach ($options['ecoles'] as $ecole) {
            $ecoles[$ecole->getNom()] = $ecole->getId();
        }
        $formBuilder
            ->add(
                'nom',
                SearchType::class,
                [
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Nom',
                    ],
                ]
            )
            ->add('ecole', ChoiceType::class, [
                'choices' => $ecoles,
                'required' => false,
                'placeholder' => 'Quel école ?',
            ]);
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'ecoles' => [],
            ]
        );
        $optionsResolver->addAllowedTypes('ecoles', 'array');
    }
}
