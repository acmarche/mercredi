<?php

namespace AcMarche\Mercredi\Tuteur\Form;

use AcMarche\Mercredi\Data\MercrediConstantes;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Security\MercrediSecurity;
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
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $isAdmin = !$this->security->isGranted(MercrediSecurity::ROLE_ADMIN);

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
                    'required' => $isAdmin,
                ]
            )
            ->add(
                'code_postal',
                IntegerType::class,
                [
                    'required' => $isAdmin,
                ]
            )
            ->add(
                'localite',
                TextType::class,
                [
                    'required' => $isAdmin,
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'required' => $isAdmin,
                ]
            )
            ->add(
                'telephone',
                TextType::class,
                [
                    'required' => $isAdmin,
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
                'remarque',
                TextareaType::class,
                [
                    'required' => false,
                    'attr' => ['rows' => 4],
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
            )
            ->add(
                'conjoint',
                ConjointType::class,
                [
                    'data_class' => Tuteur::class,
                ]
            );
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
