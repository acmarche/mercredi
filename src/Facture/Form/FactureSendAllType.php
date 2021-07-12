<?php

namespace AcMarche\Mercredi\Facture\Form;

use AcMarche\Mercredi\Form\Type\MonthWidgetType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

final class FactureSendAllType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->remove('to')
            ->add(
                'mois',
                MonthWidgetType::class,
                [
                    'required' => true,
                    'help' => 'Envoyer les factures du mois de',
                ]
            );
    }

    public function getParent()
    {
        return FactureSendType::class;
    }

}
