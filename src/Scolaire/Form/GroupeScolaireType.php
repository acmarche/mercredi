<?php

namespace AcMarche\Mercredi\Scolaire\Form;

use AcMarche\Mercredi\Entity\GroupeScolaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class GroupeScolaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add('nom')
            ->add(
                'isPlaine',
                CheckboxType::class,
                [
                    'label' => 'Groupe pour les plaines ?',
                ]
            )
            ->add('age_minimum')
            ->add('age_maximum')
            ->add('remarque');
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => GroupeScolaire::class,
            ]
        );
    }
}
