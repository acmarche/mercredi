<?php

namespace AcMarche\Mercredi\Tuteur\Form;

use AcMarche\Mercredi\Data\MercrediConstantes;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Security\MercrediSecurity;
use Symfony\Component\Form\AbstractType;
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
    /**
     * @var Security
     */
    private $security;
    /**
     * @var string
     */
    private const REQUIRED = 'required';

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $isAdmin = ! $this->security->isGranted(MercrediSecurity::ROLE_ADMIN);

        $formBuilder
            ->add(
                'nom',
                TextType::class,
                [
                    self::REQUIRED => true,
                ]
            )
            ->add(
                'prenom',
                TextType::class,
                [
                    self::REQUIRED => true,
                ]
            )
            ->add(
                'rue',
                TextType::class,
                [
                    self::REQUIRED => $isAdmin,
                ]
            )
            ->add(
                'code_postal',
                IntegerType::class,
                [
                    self::REQUIRED => $isAdmin,
                ]
            )
            ->add(
                'localite',
                TextType::class,
                [
                    self::REQUIRED => $isAdmin,
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    self::REQUIRED => $isAdmin,
                ]
            )
            ->add(
                'telephone',
                TextType::class,
                [
                    self::REQUIRED => $isAdmin,
                ]
            )
            ->add(
                'telephone_bureau',
                TextType::class,
                [
                    self::REQUIRED => false,
                ]
            )
            ->add(
                'gsm',
                TextType::class,
                [
                    self::REQUIRED => false,
                ]
            )
            ->add(
                'sexe',
                ChoiceType::class,
                [
                    self::REQUIRED => false,
                    'choices' => MercrediConstantes::SEXES,
                    'placeholder' => 'Choisissez le sexe',
                ]
            )
            ->add(
                'remarque',
                TextareaType::class,
                [
                    self::REQUIRED => false,
                    'attr' => ['rows' => 4],
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
