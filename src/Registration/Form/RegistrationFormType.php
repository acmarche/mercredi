<?php

namespace AcMarche\Mercredi\Registration\Form;

use AcMarche\Mercredi\User\Form\UserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Constraints\IsTrue;

final class RegistrationFormType extends AbstractType
{
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $url = $this->router->generate('mercredi_front_modalite');

        $formBuilder
            ->remove('roles')
            ->add(
                'telephone',
                TextType::class,
                [
                    'label' => 'Téléphone',
                    'required' => true,
                ]
            )
            ->add(
                'agreeTerms',
                CheckboxType::class,
                [
                    'label' => 'Conditions d\'utilisation',
                    'help_html' => true,
                    'help' => '<a href="'.$url.'" target="_blank">Lire les conditions</a>',
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
