<?php

namespace AcMarche\Mercredi\Presence\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SearchPresenceByMonthType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'mois',
                TextType::class,
                [
                    'attr' => ['placeholder' => 'Exemple: 05/2020'],
                  //  'help' => 'Exemple: 05/2020',
                ]
            );
    }
}
