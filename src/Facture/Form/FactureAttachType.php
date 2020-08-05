<?php

namespace AcMarche\Mercredi\Facture\Form;

use AcMarche\Mercredi\Entity\Facture\Facture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FactureAttachType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Facture::class,
            ]
        );
    }
}
