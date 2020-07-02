<?php

namespace AcMarche\Mercredi\Relation\Form;

use AcMarche\Mercredi\Data\MercrediConstantes;
use AcMarche\Mercredi\Entity\Relation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RelationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'type',
                TextType::class,
                [
                    'label' => '',
                    'help' => 'Papa, maman, oncle, belle-maman...)',
                    'required' => false,
                ]
            )
            ->add(
                'ordre',
                ChoiceType::class,
                [
                    'required' => false,
                    'choices' => MercrediConstantes::ORDRES,
                    'help' => 'Permet de forcer l\'ordre si celui est différent (Famille recomposée)',
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Relation::class,
            ]
        );
    }
}
