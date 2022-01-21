<?php

namespace AcMarche\Mercredi\Message\Form;

use AcMarche\Mercredi\Ecole\Repository\EcoleRepository;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Entity\Scolaire\Ecole;
use AcMarche\Mercredi\Jour\Repository\JourRepository;
use AcMarche\Mercredi\Plaine\Repository\PlaineRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

final class SearchMessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'jour',
                EntityType::class,
                [
                    'class' => Jour::class,
                    'placeholder' => 'Choisissez une date',
                    'required' => false,
                    'query_builder' => fn (JourRepository $jourRepository) => $jourRepository->getQlNotPlaine(),
                    //todo display name day
                    'group_by' => fn ($jour, $key, $id) => $jour->getDateJour()->format('Y'),
                ]
            )
            ->add(
                'ecole',
                EntityType::class,
                [
                    'required' => false,
                    'placeholder' => 'Choisissez une école',
                    'attr' => [
                        'class' => 'sr-only',
                    ],
                    'class' => Ecole::class,
                    'query_builder' => fn (EcoleRepository $ecoleRepository) => $ecoleRepository->getQbForListing(),
                ]
            )
            ->add(
                'plaine',
                EntityType::class,
                [
                    'required' => false,
                    'placeholder' => 'Choisissez une plaine',
                    'attr' => [
                        'class' => 'sr-only',
                    ],
                    'class' => Plaine::class,
                    'query_builder' => fn (PlaineRepository $plaineRepository) => $plaineRepository->getQbForListing(),
                ]
            );
    }
}
