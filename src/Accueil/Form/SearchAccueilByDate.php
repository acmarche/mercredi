<?php

namespace AcMarche\Mercredi\Accueil\Form;

use AcMarche\Mercredi\Accueil\Contrat\AccueilInterface;
use AcMarche\Mercredi\Entity\Scolaire\Ecole;
use AcMarche\Mercredi\Form\Type\DateWidgetType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SearchAccueilByDate extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'date_jour',
                DateWidgetType::class,
                [
                    'label' => 'Date',
                    'required' => true,
                ]
            )
            ->add(
                'heure',
                ChoiceType::class,
                [
                    'label' => 'Quand',
                    'placeholder' => 'Matin ou soir',
                    'choices' => array_flip(AccueilInterface::HEURES),
                    'required' => false,
                ]
            )
            ->add(
                'ecole',
                EntityType::class,
                [
                    'class' => Ecole::class,
                    'required' => false,
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
            ]
        );
    }
}
