<?php

namespace AcMarche\Mercredi\Search\Form;

use AcMarche\Mercredi\Parameter\Option;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;

final class SearchEnfantEcoleType extends AbstractType
{
    public function __construct(private ParameterBagInterface $parameterBag)
    {
    }

    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
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
            );
        if ($this->parameterBag->get(Option::ACCUEIL) > 1) {
            $formBuilder
                ->add(
                    'accueil',
                    CheckboxType::class,
                    [
                        'label' => 'Inscrit aux accueils',
                        'required' => false,
                        'attr' => [
                            'placeholder' => 'Nom',
                        ],
                    ]
                );
        }
    }
}
