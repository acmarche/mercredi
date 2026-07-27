<?php

namespace AcMarche\Mercredi\Tuteur\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;

final class AcceptMigrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'acceptMigration',
                CheckboxType::class,
                [
                    'label' => 'J\'accepte le transfert de mes données vers la nouvelle plateforme',
                    'required' => true,
                    'mapped' => false,
                    'constraints' => [
                        new IsTrue(
                            [
                                'message' => 'Vous devez cocher la case pour accepter la migration.',
                            ]
                        ),
                    ],
                ]
            );
    }
}
