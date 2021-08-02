<?php

namespace AcMarche\Mercredi\Presence\Form;

use AcMarche\Mercredi\Ecole\Repository\EcoleRepository;
use AcMarche\Mercredi\Entity\Scolaire\Ecole;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Jour\Repository\JourRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

final class SearchPresenceType extends AbstractType
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
                    'query_builder' => fn(JourRepository $jourRepository) => $jourRepository->getQlNotPlaine(),
                    //todo display name day
                    'group_by' => fn($jour, $key, $id) => $jour->getDateJour()->format('Y'),
                ]
            )
            ->add(
                'ecole',
                EntityType::class,
                [
                    'class' => Ecole::class,
                    'query_builder' => fn(EcoleRepository $ecoleRepository) => $ecoleRepository->getQbForListing(),
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
