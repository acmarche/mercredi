<?php

namespace AcMarche\Mercredi\Facture\Form;

use AcMarche\Mercredi\Entity\Facture\Facture;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PresenceFactureAttachType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $factures = $options['factures'];

        $builder->add('facture', EntityType::class, [
            'class' => Facture::class,
            'choices' => $factures,
            'placeholder' => 'Sélectionnez une facture',
            'choice_label' => function (Facture $facture): string {
                return $facture->getTuteur().' du '.$facture->getMois();
            },
        ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'factures' => [],
            ]
        )
            ->setAllowedTypes('factures', 'array');
    }
}
