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

final class PresenceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
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

        $formBuilder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event): void {
                $form = $event->getForm();
                /** @var Presence $presence */
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

        $formBuilder->addEventSubscriber(new AddFieldTuteurSubscriber());
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => Presence::class,
            ]
        );
    }
}
