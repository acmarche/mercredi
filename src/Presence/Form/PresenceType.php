<?php

namespace AcMarche\Mercredi\Presence\Form;

use AcMarche\Mercredi\Data\MercrediConstantes;
use AcMarche\Mercredi\Entity\Presence;
use AcMarche\Mercredi\Form\Type\OrdreType;
use AcMarche\Mercredi\Form\Type\RemarqueType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
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

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $form = $event->getForm();
                /**
                 * @var Presence $presence
                 */
                $presence = $event->getData();
                $jour = $presence->getJour();
                if ($jour->isPedagogique()) {
                    $form->add(
                        'half',
                        CheckboxType::class,
                        [
                            'label' => 'Demi-journée',
                            'required' => false,
                            'help' => "L'enfant a été présent une demi-journée",
                        ]
                    );
                }
            }
        );
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
