<?php

namespace AcMarche\Mercredi\Scolaire\Form;

use AcMarche\Mercredi\Entity\AnneeScolaire;
use AcMarche\Mercredi\Entity\GroupeScolaire;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnneeScolaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add(
                'ordre',
                IntegerType::class,
                [
                ]
            )
            ->add(
                'annee_suivante',
                EntityType::class,
                [
                    'class' => AnneeScolaire::class,
                    'label' => 'Année suivante',
                    'help' => 'Pour le passage d\'année',
                    'required' => false,
                ]
            )
            ->add(
                'groupe_scolaire',
                EntityType::class,
                [
                    'class' => GroupeScolaire::class,
                    'required' => false,
                ]
            )
            ->add('remarque');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => AnneeScolaire::class,
            ]
        );
    }
}
