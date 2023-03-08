<?php

namespace AcMarche\Mercredi\Tuteur\Form;

use AcMarche\Mercredi\Data\MercrediConstantes;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Form\Type\RegistryNumberType;
use AcMarche\Mercredi\Security\Role\MercrediSecurityRole;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

final class TuteurType extends AbstractType
{
    public function __construct(
        private Security $security
    ) {
    }

    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $isAdmin = $this->security->isGranted(MercrediSecurityRole::ROLE_ADMIN);

        $formBuilder
            ->add(
                'nom',
                TextType::class,
                [
                    'required' => true,
                ]
            )
            ->add(
                'prenom',
                TextType::class,
                [
                    'required' => true,
                ]
            )
            ->add(
                'rue',
                TextType::class,
                [
                    'required' => !$isAdmin,
                ]
            )
            ->add(
                'code_postal',
                IntegerType::class,
                [
                    'required' => !$isAdmin,
                ]
            )
            ->add(
                'localite',
                TextType::class,
                [
                    'required' => !$isAdmin,
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'required' => !$isAdmin,
                ]
            )
            ->add(
                'registre_national',
                RegistryNumberType::class,
                [
                    'required' => !$isAdmin,
                ]
            )
            ->add(
                'telephone',
                TextType::class,
                [
                    'required' => !$isAdmin,
                ]
            )
            ->add(
                'telephone_bureau',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'gsm',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'sexe',
                ChoiceType::class,
                [
                    'required' => false,
                    'choices' => MercrediConstantes::SEXES,
                    'placeholder' => 'Choisissez le sexe',
                ]
            )
            ->add(
                'iban',
                TextType::class,
                [
                    'required' => false,
                    'help' => 'Compte bancaire pour les remboursements',
                ]
            )
            ->add(
                'remarque',
                TextareaType::class,
                [
                    'required' => false,
                    'attr' => [
                        'rows' => 4,
                    ],
                ]
            )
            ->add(
                'conjoint',
                ConjointType::class,
                [
                    'data_class' => Tuteur::class,
                ]
            )
            ->add(
                'facturePapier',
                CheckboxType::class,
                [
                    'required' => false,
                    'label' => 'Facture papier',
                    'help' => 'Recevoir une copie papier ?',
                ]
            );
        if ($isAdmin) {
            $formBuilder->add(
                'createAccount',
                CheckboxType::class,
                [
                    'label' => 'Créer un compte',
                    'required' => false,
                    'help' => 'Si vous cochez cette case, un compte sera créé. Le parent recevra par mail les informations de connexion',
                ]
            );
        }
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => Tuteur::class,
            ]
        );
    }
}
