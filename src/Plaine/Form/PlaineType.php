<?php

namespace AcMarche\Mercredi\Plaine\Form;

use AcMarche\Mercredi\Entity\Plaine\Plaine;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PlaineType extends AbstractType
{
    /**
     * @var string
     */
    private const REQUIRED = 'required';
    /**
     * @var string
     */
    private const LABEL = 'label';
    /**
     * @var string
     */
    private const HELP = 'help';
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add('nom')
            ->add(
                'prix1',
                MoneyType::class,
                [
                    self::REQUIRED => true,
                    self::LABEL => 'Prix 1er enfant',
                    self::HELP => 'Uniquement les chiffres',
                ]
            )
            ->add(
                'prix2',
                MoneyType::class,
                [
                    self::REQUIRED => true,
                    self::LABEL => 'Prix 2iem enfant et suivant',
                    self::HELP => 'Uniquement les chiffres',
                ]
            )

            ->add(
                'plaine_groupes',
                CollectionType::class,
                [
                    'entry_type' => PlaineGroupeType::class,
                    self::LABEL => 'Maximum par groupe',
                ]
            )
            ->add(
                'prematernelle',
                CheckboxType::class,
                [
                    self::REQUIRED => false,
                    self::LABEL => 'Distinguer les prématernelles pour le listing ?',
                ]
            )
            ->add(
                'inscriptionOpen',
                CheckboxType::class,
                [
                    self::REQUIRED => false,
                    self::LABEL => 'Ouvrir les inscriptions',
                    self::HELP => 'Si cette case est cochée, les parents pourront inscrire leurs enfants à la plaine',
                ]
            )
            ->add(
                'remarque',
                TextareaType::class,
                [
                    self::REQUIRED => false,
                    'attr' => ['rows' => 8],
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => Plaine::class,
            ]
        );
    }
}
