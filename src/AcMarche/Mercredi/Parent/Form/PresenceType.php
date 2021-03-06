<?php

namespace AcMarche\Mercredi\Parent\Form;

use AcMarche\Mercredi\Admin\Entity\Presence;
use AcMarche\Mercredi\Admin\Form\Type\JourHiddenType;
use AcMarche\Mercredi\Plaine\Form\Type\EnfantSelectorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PresenceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('enfant', EnfantSelectorType::class)
            ->add('jour', JourHiddenType::class)
            ->add(
                'remarques',
                TextareaType::class,
                [
                    'label' => 'Remarque (Facultatif)',
                    'required' => false,
                    'help' => 'Exemple: Mon enfant repart avec telle personne ou je les reprends à 15h car rdv médical ',
                ]
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
