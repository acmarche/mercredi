<?php

namespace AcMarche\Mercredi\Enfant\Form;

use AcMarche\Mercredi\Entity\Ecole;
use AcMarche\Mercredi\Scolaire\ScolaireData;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;

class SearchEnfantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'nom',
                SearchType::class,
                [
                    'required' => false,
                    'attr' => ['placeholder' => 'Nom'],
                ]
            )
            ->add(
                'ecole',
                EntityType::class,
                [
                    'class' => Ecole::class,
                    'required' => false,
                    'placeholder' => 'Choisissez une école',
                    'attr' => ['class' => 'custom-select my-1 mr-sm-2'],
                ]
            )
            ->add(
                'annee_scolaire',
                ChoiceType::class,
                [
                    'choices' => array_combine(ScolaireData::ANNEES_SCOLAIRES, ScolaireData::ANNEES_SCOLAIRES),
                    'required' => false,
                    'label' => 'Année scolaire',
                    'placeholder' => 'Choisissez son année scolaire',
                    'attr' => ['class' => 'custom-select my-1 mr-sm-2'],
                ]
            );
    }
}
