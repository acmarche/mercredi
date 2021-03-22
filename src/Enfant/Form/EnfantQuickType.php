<?php

namespace AcMarche\Mercredi\Enfant\Form;

use AcMarche\Mercredi\Entity\AnneeScolaire;
use AcMarche\Mercredi\Entity\Ecole;
use AcMarche\Mercredi\Entity\Enfant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class EnfantQuickType extends AbstractType
{
    /**
     * @var string
     */
    private const REQUIRED = 'required';

    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $year = new \DateTime('today');
        $year = $year->format('Y');

        $formBuilder
            ->add(
                'nom',
                TextType::class,
                [
                    self::REQUIRED => true,
                ]
            )
            ->add(
                'prenom',
                TextType::class,
                [
                    self::REQUIRED => true,
                ]
            )
            ->add(
                'birthday',
                BirthdayType::class,
                [
                    'label' => 'Né le',
                    self::REQUIRED => false,
                    'years' => range($year - 15, $year),
                ]
            )
            ->add(
                'ecole',
                EntityType::class,
                [
                    'class' => Ecole::class,
                    self::REQUIRED => true,
                    'placeholder' => 'Choisissez son école',
                ]
            )
            ->add(
                'annee_scolaire',
                EntityType::class,
                [
                    'class' => AnneeScolaire::class,
                    'label' => 'Année scolaire',
                    'placeholder' => 'Choisissez son année scolaire',
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => Enfant::class,
            ]
        );
    }
}
