<?php

namespace AcMarche\Mercredi\Reduction\Form;

use AcMarche\Mercredi\Entity\Reduction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReductionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add(
                'pourcentage',
                PercentType::class,
                [
                    'label' => 'Pourcentage',
                    'required' => false,
                    'type' => 'integer',
                    'help' => 'Uniquement les chiffres',
                ]
            )->add(
                'forfait',
                MoneyType::class,
                [
                    'required' => false,
                    'help' => 'Montant du forfait, uniquement les chiffres',
                ]
            )
            ->add(
                'remarque',
                TextareaType::class,
                [
                    'required' => false,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Reduction::class,
            ]
        );
    }
}
