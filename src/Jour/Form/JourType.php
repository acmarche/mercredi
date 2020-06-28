<?php

namespace AcMarche\Mercredi\Jour\Form;

use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Form\Type\ArchivedType;
use AcMarche\Mercredi\Form\Type\DateWidgetType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
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
                DateWidgetType::class,
                [
                    'label' => 'Date du jour de garde',
                ]
            )
            ->add(
                'pedagogique',
                CheckboxType::class,
                [
                    'label' => 'Journée pédagoque',
                    'required' => false,
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
