<?php

namespace AcMarche\Mercredi\Plaine\Form;

use AcMarche\Mercredi\Entity\GroupeScolaire;
use AcMarche\Mercredi\Entity\Plaine\PlaineGroupe;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlaineGroupeType extends AbstractType
{
    protected $label = false;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'groupeScolaire',
                EntityType::class,
                [
                    'class' => GroupeScolaire::class,
                    'attr' => ['readonly' => true],
                    'label' => false,
                ]
            )
            ->add('inscription_maximum', IntegerType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => PlaineGroupe::class,
            ]
        );
    }
}
