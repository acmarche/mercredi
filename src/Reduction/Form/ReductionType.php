<?php

namespace AcMarche\Mercredi\Reduction\Form;

use AcMarche\Mercredi\Entity\Reduction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ReductionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
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
            )
            ->add(
                'amount',
                MoneyType::class,
                [
                    'label' => 'Montant fixe',
                    'required' => false,
                    'help' => 'Montant fixe de la réduction, uniquement les chiffres',
                ]
            )
            ->add(
                'is_forfait',
                CheckboxType::class,
                [
                    'label' => 'Appliquer un forfait',
                    'required' => false,
                    'help' => 'Un forfait sera appliqué et non une réduction ! Uniquement valable pour un montant fixe',
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

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Reduction::class,
            ]
        );
    }
}
