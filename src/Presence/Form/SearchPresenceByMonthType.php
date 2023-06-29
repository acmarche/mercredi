<?php

namespace AcMarche\Mercredi\Presence\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class SearchPresenceByMonthType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'mois',
                TextType::class,
                [
                    'attr' => [
                        'placeholder' => '05/2020',
                        'autocomplete' => 'off',
                    ],
                    'help' => 'Exemple: 05/2020',
                ]
            );

        $builder->addEventSubscriber(new AddFieldSearchPlaineSubscriber());
    }
}
