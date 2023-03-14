<?php

namespace AcMarche\Mercredi\Form\Type;

use AcMarche\Mercredi\Validator\RegistryNumber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistryNumberType extends AbstractType
{
    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'required' => true,
                'label' => 'Numéro national',
                'help' => 'Uniquement les chiffres. (11 chiffres)',
                'constraints' => [new RegistryNumber()],
                'attr' => ['autocomplete' => 'off'],
            ]
        );
    }

    public function getParent(): ?string
    {
        return TextType::class;
    }
}
