<?php

namespace AcMarche\Mercredi\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RemarqueType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'attr' => ['rows' => 3],
                'required' => false,
            ]
        );
    }

    public function getParent()
    {
        return TextareaType::class;
    }
}
