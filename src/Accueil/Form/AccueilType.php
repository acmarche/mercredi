<?php

namespace AcMarche\Mercredi\Accueil\Form;

use AcMarche\Mercredi\Accueil\Service\AccueilService;
use AcMarche\Mercredi\Entity\Accueil;
use AcMarche\Mercredi\Form\Type\DateWidgetType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;

class AccueilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
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
                'heure',
                ChoiceType::class,
                [
                    'label' => 'Quand',
                    'placeholder' => 'Matin ou soir',
                    'choices' => AccueilService::getMatinSoir(),
                    'multiple' => false,
                    'expanded' => false,
                    'required' => true,
                ]
            )
            ->add(
                'duree',
                IntegerType::class,
                [
                    'label' => 'Temps resté',
                    'help' => 'Nombre de demi heure que l\'enfant est resté',
                    'constraints' => [
                        new GreaterThan(
                            [
                                'value' => 0,
                            ]
                        ),
                    ],
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

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Accueil::class,
            ]
        );
    }
}
