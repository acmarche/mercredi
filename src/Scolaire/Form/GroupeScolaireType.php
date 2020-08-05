<?php

namespace AcMarche\Mercredi\Scolaire\Form;

use AcMarche\Mercredi\Entity\GroupeScolaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GroupeScolaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('age_minimum')
            ->add('age_maximum')
            ->add('remarque');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => GroupeScolaire::class,
            ]
        );
    }
}
