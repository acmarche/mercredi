<?php

namespace AcMarche\Mercredi\Search\Form;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\AbstractType;
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
    }
}
