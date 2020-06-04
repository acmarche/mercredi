<?php

namespace AcMarche\Mercredi\Jour\Form;

use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Form\Type\ArchivedType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JourType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'date_jour',
                DateType::class,
                [
                    'label' => 'Date du jour de garde',
                    'widget' => 'single_text',
                    'required' => true,
                    'attr' => ['autocomplete' => 'off'],
                ]
            )
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
                    'label' => 'Prix 2iem enfant',
                    'help' => 'Uniquement les chiffres',
                ]
            )
            ->add(
                'prix3',
                MoneyType::class,
                [
                    'required' => true,
                    'label' => 'Prix des suivants',
                    'help' => 'Uniquement les chiffres',
                ]
            )
            ->add(
                'color',
                ColorType::class,
                [
                    'required' => false,
                    'label' => 'Couleur',
                ]
            )
            ->add(
                'archived',
                ArchivedType::class,
                [
                    'help' => 'En archivant la date ne sera plus proposée lors de l\'ajout d\'une présence',
                ]
            )
            ->add(
                'remarque',
                TextareaType::class,
                [
                    'required' => false,
                    'label' => 'Remarques',
                    'help' => 'Cette donnée est visible par les parents et dans le listing des présences',
                    'attr' => [
                        'rows' => 5,
                    ],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Jour::class,
            ]
        );
    }
}
