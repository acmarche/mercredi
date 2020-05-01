<?php

namespace AcMarche\Mercredi\Presence\Form;

use AcMarche\Mercredi\Data\MercrediConstantes;
use AcMarche\Mercredi\Entity\Presence;
use AcMarche\Mercredi\Form\Type\OrdreType;
use AcMarche\Mercredi\Form\Type\RemarqueType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PresenceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'absent',
                ChoiceType::class,
                [
                    'choices' => MercrediConstantes::getAbsenceTxt(),
                ]
            )
            ->add('ordre', OrdreType::class)
            ->add('remarque', RemarqueType::class)
//            ->add('tuteur')
            ->add('reduction');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Presence::class,
            ]
        );
    }
}
