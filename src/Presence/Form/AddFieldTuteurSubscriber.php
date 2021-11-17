<?php

namespace AcMarche\Mercredi\Presence\Form;

use AcMarche\Mercredi\Entity\Presence\Accueil;
use AcMarche\Mercredi\Entity\Presence\Presence;
use AcMarche\Mercredi\Entity\Tuteur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AddFieldTuteurSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [FormEvents::PRE_SET_DATA => 'preSetData'];
    }

    public function preSetData(FormEvent $event)
    {
        /**
         * @var Presence|Accueil $presence
         */
        $presence = $event->getData();
        $form = $event->getForm();

        $enfant = $presence->getEnfant();
        $relations = $enfant->getRelations();

        if (count($relations) > 1) {
            $tuteurs = array_map(
                fn($relation) => $relation->getTuteur(),
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
