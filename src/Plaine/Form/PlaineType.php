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

class PlaineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add(
                'prix1',
                MoneyType::class,
                [
                    'required' => true,
                    'label' => 'Prix 1er enfant',
                    'help' => 'Uniquement les chiffres',
                ]
            )
            ->add(
                'prix2',
                MoneyType::class,
                [
                    'required' => true,
                    'label' => 'Prix 2iem enfant et suivant',
                    'help' => 'Uniquement les chiffres',
                ]
            )

            ->add(
                'plaine_groupes',
                CollectionType::class,
                [
                    'entry_type' => PlaineGroupeType::class,
                    'label' => 'Maximum par groupe',
                ]
            )
            ->add(
                'prematernelle',
                CheckboxType::class,
                [
                    'required' => false,
                    'label' => 'Distinguer les prématernelles pour le listing ?',
                ]
            )
            ->add(
                'inscriptionOpen',
                CheckboxType::class,
                [
                    'required' => false,
                    'label' => 'Ouvrir les inscriptions',
                    'help' => 'Si cette case est cochée, les parents pourront inscrire leurs enfants à la plaine',
                ]
            )
            ->add(
                'remarque',
                TextareaType::class,
                [
                    'required' => false,
                    'attr' => ['rows' => 8],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Plaine::class,
            ]
        );
    }
}
