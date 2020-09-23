<?php


namespace AcMarche\Mercredi\Accueil\Form;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class TuteurSubsciberFieldType implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    { return [
            FormEvents::PRE_SET_DATA => 'OnPreSetData',
        ];

    }
    /**
     * Verifie si nouveau objet
     * Si id enfant avec ou pas.
     */
    public function OnPreSetData(FormEvent $event)
    {
        /**
         * @var Presence
         */
        $presence = $event->getData();
        $enfant = $presence->getEnfant();

        if ($enfant) {
            $tuteurs = $this->enfantTuteurRepository->getTuteursByEnfant($enfant);
            $form = $event->getForm();

            //new
            if (!$presence || null === $presence->getId()) {
                $form->add(
                    'jours',
                    EntityType::class,
                    [
                        'class' => Jour::class,
                        'multiple' => true,
                        'query_builder' => function (JourRepository $cr) use ($enfant) {
                            return $cr->getForList($enfant);
                        },
                        'label' => 'Choisissez une ou plusieurs dates',
                    ]
                );

                if (count($tuteurs) > 1) {
                    $form->add(
                        'tuteur',
                        EntityType::class,
                        [
                            'choices' => $tuteurs,
                            'class' => Tuteur::class,
                        ]
                    );
                } else {
                    $presence->setTuteur($tuteurs[0]);
                    $form->add('tuteur', HiddenType::class);
                }
            }
        }
    }
}
