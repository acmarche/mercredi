<?php

namespace AcMarche\Mercredi\Presence\Form;

use AcMarche\Mercredi\Ecole\Repository\EcoleRepository;
use AcMarche\Mercredi\Entity\Ecole;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Jour\Repository\JourRepository;
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
                    'query_builder' => function (JourRepository $jourRepository) {
                        return $jourRepository->getQbForListing();
                    },
                    //todo display name day
                    'group_by' => function ($jour, $key, $id) {
                        return $jour->getDateJour()->format('Y');
                    },
                ]
            )
            ->add(
                'ecole',
                EntityType::class,
                [
                    'class' => Ecole::class,
                    'query_builder' => function (EcoleRepository $ecoleRepository) {
                        return $ecoleRepository->getQbForListing();
                    },
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
