<?php

namespace AcMarche\Mercredi\Presence\Form;

use AcMarche\Mercredi\Entity\Tuteur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AddFieldTuteurSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        // Tells the dispatcher that you want to listen on the form.pre_set_data
        // event and that the preSetData method should be called.
        return [FormEvents::PRE_SET_DATA => 'preSetData'];
    }

    public function preSetData(FormEvent $event)
    {
        /**
         * @var \AcMarche\Mercredi\Entity\Presence $presence
         */
        $presence = $event->getData();
        $form = $event->getForm();

        $enfant = $presence->getEnfant();
        $relations = $enfant->getRelations();
        if (count($relations) > 1) {
            $tuteurs = array_map(
                function ($relation) {
                    return $relation->getTuteur();
                },
                $relations->toArray(),
            );
            $form
                ->add(
                    'tuteur',
                    EntityType::class,
                    [
                        'class' => Tuteur::class,
                        'choices' => $tuteurs,
                        'required' => true,
                        'label' => 'Parent',
                        'placeholder' => 'Choisissez un parent',
                        'help' => 'Sous la garde de',
                    ]
                );
        }
    }
}