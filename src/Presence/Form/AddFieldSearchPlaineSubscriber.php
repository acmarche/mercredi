<?php

namespace AcMarche\Mercredi\Presence\Form;

use AcMarche\Mercredi\Presence\Utils\PresenceUtils;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AddFieldSearchPlaineSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
        ];
    }

    /**
     * @param FormEvent $event
     * @return void
     */
    public function preSetData(FormEvent $event): void
    {
        $form = $event->getForm();
        //$types
        //?bool $filter null => only mercredi, true only plaine, false both

        $form
            ->add(
                'filter',
                ChoiceType::class,
                [
                    'required' => true,
                    'help' => 'Filtrer',
                    'label' => 'Quoi',
                    'choices' => PresenceUtils::types,
                    'placeholder' => 'Sélectionnez',
                ]
            );
    }
}
