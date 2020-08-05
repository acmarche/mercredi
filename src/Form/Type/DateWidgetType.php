<?php

namespace AcMarche\Mercredi\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateWidgetType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'widget' => 'single_text',
                'required' => true,
                'attr' => ['autocomplete' => 'off'],
            ]
        );
    }

    public function getParent()
    {
        return DateType::class;
    }
}
