<?php

namespace AcMarche\Mercredi\Facture\Form;

use AcMarche\Mercredi\Entity\Facture\FactureCron;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class FactureCronType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'done',
                CheckboxType::class,
                [
                    'label' => 'Terminé',
                    'required' => false,
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => FactureCron::class,
            ]
        );
    }
}
