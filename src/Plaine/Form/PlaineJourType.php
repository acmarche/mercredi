<?php

namespace AcMarche\Mercredi\Plaine\Form;

use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Jour\Form\JourDateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlaineJourType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'jours',
                CollectionType::class,
                [
                    'entry_type' => JourDateType::class,
                    'entry_options' => ['label' => false],
                    'allow_add' => true,
                    'allow_delete' => true,
                    'required' => false,
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
