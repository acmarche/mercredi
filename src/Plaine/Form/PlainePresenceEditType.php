<?php

namespace AcMarche\Mercredi\Plaine\Form;

use AcMarche\Mercredi\Data\MercrediConstantes;
use AcMarche\Mercredi\Entity\Presence;
use AcMarche\Mercredi\Form\Type\OrdreType;
use AcMarche\Mercredi\Form\Type\RemarqueType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlainePresenceEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'absent',
                ChoiceType::class,
                [
                    'choices' => array_flip(MercrediConstantes::getListAbsences()),
                ]
            )
            ->add(
                'ordre',
                OrdreType::class,
                [
                    'help' => 'En forçant l\ordre, la fraterie présente ne sera pas tenu en compte',
                ]
            )
            ->add('remarque', RemarqueType::class)
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
