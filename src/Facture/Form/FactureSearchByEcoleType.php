<?php

namespace AcMarche\Mercredi\Facture\Form;

use AcMarche\Mercredi\Entity\Scolaire\Ecole;
use AcMarche\Mercredi\Form\Type\MonthWidgetType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class FactureSearchByEcoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'mois',
                MonthWidgetType::class,
                [
                    'help' => null,
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Mois facture, format: 06-2021',
                    ],
                ]
            )
            ->add(
                'ecole',
                EntityType::class,
                [
                    'class' => Ecole::class,
                    'required' => false,
                    'placeholder' => 'Choisissez une école',
                    'attr' => [
                        'class' => 'custom-select my-1 mr-sm-2',
                    ],
                ]
            );
    }
}
