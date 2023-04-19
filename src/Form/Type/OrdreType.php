<?php

namespace AcMarche\Mercredi\Form\Type;

use AcMarche\Mercredi\Data\MercrediConstantes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class OrdreType extends AbstractType
{
    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'required' => false,
                'choices' => MercrediConstantes::ORDRES,
                'attr' => [
                    'class' => 'custom-select my-1 mr-sm-2',
                ],
                'help' => 'Ordre établi suivant les dates de naissance. Mettez une valeur ici pour forcer l\'ordre',
            ]
        );
    }

    public function getParent(): ?string
    {
        return ChoiceType::class;
    }
}
