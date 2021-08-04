<?php

namespace AcMarche\Mercredi\Plaine\Form;

use AcMarche\Mercredi\Entity\Scolaire\GroupeScolaire;
use AcMarche\Mercredi\Entity\Plaine\PlaineGroupe;
use AcMarche\Mercredi\Scolaire\Repository\GroupeScolaireRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

final class PlaineGroupeType extends AbstractType
{
    protected bool $label = false;

    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'groupeScolaire',
                EntityType::class,
                [
                    'class' => GroupeScolaire::class,
                    'query_builder' => function (GroupeScolaireRepository $groupeScolaireRepository) {
                        return $groupeScolaireRepository->getQbForListingPlaine();
                    },
                    'attr' => ['readonly' => true],
                    'label' => false,
                ]
            )
            ->add('inscription_maximum', IntegerType::class, [
                'label' => 'Nombre maximum d\'inscrits',
            ])
            ->add(
                'file',
                VichFileType::class,
                [
                    'label' => 'Fichier',
                    'help' => 'Uniquement images ou pdf. Fichier qui sera envoyé aux parents pour les modalités',
                    'required' => false,
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => PlaineGroupe::class,
            ]
        );
    }
}
