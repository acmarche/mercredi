<?php

namespace AcMarche\Mercredi\Facture\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

final class FactureSendAllType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->remove('to')
            ->add('force', CheckboxType::class, [
                'required' => false,
                'label' => 'Forcer l\'envoi',
                'help' => 'Si la case est cochée, les factures qui ont déjà été envoyées le seront à nouveau',
            ]);
    }

    public function getParent(): ?string
    {
        return FactureSendType::class;
    }
}
