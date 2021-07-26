<?php

namespace AcMarche\Mercredi\Scolaire\Form;

use AcMarche\Mercredi\Entity\AnneeScolaire;
use AcMarche\Mercredi\Entity\GroupeScolaire;
use AcMarche\Mercredi\Scolaire\Repository\AnneeScolaireRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class GroupeScolaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add('nom')
            ->add(
                'isPlaine',
                CheckboxType::class,
                [
                    'label' => 'Groupe pour les plaines ?',
                ]
            )
            ->add('age_minimum')
            ->add('age_maximum')
            ->add('remarque')
            ->add(
                'annees_scolaires',
                EntityType::class,
                [
                    'class' => AnneeScolaire::class,
                    'query_builder' => function (AnneeScolaireRepository $anneeScolaireRepository) {
                        $anneeScolaireRepository->getQbForListing();
                    },
                    'label' => 'Année(s) scolaire(s)',
                    'required' => false,
                    'multiple' => true,
                    'expanded' => true,
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => GroupeScolaire::class,
            ]
        );
    }
}
