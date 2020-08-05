<?php

namespace AcMarche\Mercredi\Relation\Form;

use AcMarche\Mercredi\Enfant\Form\EnfantQuickType;
use AcMarche\Mercredi\Relation\Dto\TuteurEnfantDto;
use AcMarche\Mercredi\Tuteur\Form\TuteurQuickType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TuteurEnfantQuickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'tuteur',
                TuteurQuickType::class
            )
            ->add(
                'enfant',
                EnfantQuickType::class,
                [
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => TuteurEnfantDto::class,
            ]
        );
    }
}
