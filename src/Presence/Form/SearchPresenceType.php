<?php

namespace AcMarche\Mercredi\Presence\Form;

use AcMarche\Mercredi\Entity\Ecole;
use AcMarche\Mercredi\Entity\Jour;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

class SearchPresenceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'jour',
                EntityType::class,
                [
                    'class' => Jour::class,
                    'placeholder' => 'Choisissez une date',
               /*     'group_by' => function ($choiceValue, $key, $value) {
                        list($date, $jour) = explode(' ', $key);
                        $dateTime = \DateTime::createFromFormat('j-m-Y', $date);

                        return $dateTime->format('Y');
                    },*/
                ]
            )
            ->add(
                'ecole',
                EntityType::class,
                [
                    'class' => Ecole::class,
                    'required' => false,
                    'placeholder' => 'Choisissez une école',
                    'attr' => ['class' => 'sr-only'],
                ]
            )
            ->add(
                'displayRemarque',
                CheckboxType::class,
                [
                    'label' => 'Afficher les remarques',
                    'required' => false,
                ]
            );
    }
}
