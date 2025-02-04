<?php

namespace AcMarche\Mercredi\Contact\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

final class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'nom',
                TextType::class,
                [
                    'label' => 'Votre nom',
                    'constraints' => [
                        new Length([
                            'min' => 3,
                            'minMessage' => 'Your password should be at least {{ limit }} characters',
                        ]),
                    ],
                ],
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'attr' => ['Votre email'],
                ],
            )
            ->add(
                'texte',
                TextareaType::class,
                [
                    'constraints' => [
                        new NotBlank(),
                        new Length(['min' => 3]),
                    ],
                    'attr' => [
                        'rows' => 5,
                    ],

                ],
            );
    }
}
