<?php

namespace AcMarche\Mercredi\Accueil\Form;

use AcMarche\Mercredi\Form\Type\DateWidgetType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccueilRetardAdminTpe extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'heure_retard',
                TimeType::class,
                [
                    'label' => 'Heure de départ',
                    'with_seconds' => false,
                    'hours' => [18, 19],
                ]
            )
            ->add('delete', CheckboxType::class, [
                'label' => 'Supprimer',
                'help' => 'Supprimer le retard',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                // 'data_class' => Accueil::class,
            ]
        );
    }
}
