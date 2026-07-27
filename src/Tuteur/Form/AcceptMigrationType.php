<?php

namespace AcMarche\Mercredi\Tuteur\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotNull;

final class AcceptMigrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'acceptMigration',
                ChoiceType::class,
                [
                    'label' => false,
                    'expanded' => true,
                    'multiple' => false,
                    'mapped' => false,
                    'choices' => [
                        'J\'accepte le transfert de mes données vers cette unique plateforme à savoir le site enfance-jeunesse.marche.be' => true,
                        'Je n\'accepte pas le transfert de mes données vers cette unique plateforme à savoir le site enfance-jeunesse.marche.be. Je créerai moi-même un compte sur ce site.' => false,
                    ],
                    'constraints' => [
                        new NotNull(
                            [
                                'message' => 'Vous devez choisir une des deux réponses.',
                            ]
                        ),
                    ],
                ]
            );
    }
}
