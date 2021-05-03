<?php


namespace AcMarche\Mercredi\Accueil\Form;

use AcMarche\Mercredi\Entity\Accueil;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;

class AccueilInlineTpe extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
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
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => Accueil::class,
            ]
        );
    }
}
