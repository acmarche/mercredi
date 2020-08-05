<?php

namespace AcMarche\Mercredi\Accueil\Form;

use AcMarche\Mercredi\Accueil\Service\AccueilService;
use AcMarche\Mercredi\Entity\Accueil;
use AcMarche\Mercredi\Form\Type\DateWidgetType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccueilParentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'date_jour',
                DateWidgetType::class,
                [
                    'label' => 'Date',
                ]
            )
            ->add(
                'matin_soir',
                ChoiceType::class,
                [
                    'label' => 'Quand',
                    'choices' => AccueilService::getMatinSoir(),
                ]
            )
            ->add(
                'remarque',
                TextareaType::class,
                [
                    'required' => false,
                    'attr' => ['rows' => 2],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Accueil::class,
            ]
        );
    }
}
