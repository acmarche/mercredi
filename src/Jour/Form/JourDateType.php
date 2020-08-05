<?php

namespace AcMarche\Mercredi\Jour\Form;

use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Form\Type\DateWidgetType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JourDateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'date_jour',
                DateWidgetType::class,
                [
                    'label' => false,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Jour::class,
            ]
        );
    }
}
