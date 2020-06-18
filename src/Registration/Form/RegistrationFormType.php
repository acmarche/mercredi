<?php

namespace AcMarche\Mercredi\Registration\Form;

use AcMarche\Mercredi\User\Form\UserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->remove('roles')
            ->add(
                'agreeTerms',
                CheckboxType::class,
                [
                    'label' => 'Conditions d\'utilisation',
                    'help_html' => true,
                    'help' => '<a href="#" target="_blank">Lire les conditions</a>',
                    'mapped' => false,
                    'constraints' => [
                        new IsTrue(
                            [
                                'message' => 'You should agree to our terms.',
                            ]
                        ),
                    ],
                ]
            );
    }

    public function getParent()
    {
        return UserType::class;
    }
}
