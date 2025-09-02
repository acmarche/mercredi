<?php

namespace AcMarche\Mercredi\Presence\Form;

use AcMarche\Mercredi\Presence\Utils\PresenceUtils;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AddFieldSearchPlaineSubscriber implements EventSubscriberInterface
{
    public function __construct(private ParameterBagInterface $parameterBag)
    {
    }

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

        if ($this->parameterBag->get('mercredi.plaine') > 1) {
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
        if ($this->parameterBag->get('mercredi.accueil') > 1) {
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
        } else {
            $form
                ->add(
                    'filter',
                    HiddenType::class,
                    [
                        'empty_data' => null,
                    ]
                );
        }
    }
}
