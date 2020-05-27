<?php

namespace AcMarche\Mercredi\Message\Form;

use AcMarche\Mercredi\Ecole\Repository\EcoleRepository;
use AcMarche\Mercredi\Entity\Ecole;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Jour\Repository\JourRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class SearchMessageType extends AbstractType
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
                    'required' => false,
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
                    'required' => false,
                    'placeholder' => 'Choisissez une école',
                    'attr' => ['class' => 'sr-only'],
                    'class' => Ecole::class,
                    'query_builder' => function (EcoleRepository $ecoleRepository) {
                        return $ecoleRepository->getQbForListing();
                    },
                ]
            );
    }
}
