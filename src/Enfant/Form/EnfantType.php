<?php

namespace AcMarche\Mercredi\Enfant\Form;

use DateTime;
use AcMarche\Mercredi\Data\MercrediConstantes;
use AcMarche\Mercredi\Entity\AnneeScolaire;
use AcMarche\Mercredi\Entity\Ecole;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\GroupeScolaire;
use AcMarche\Mercredi\Form\Type\OrdreType;
use AcMarche\Mercredi\Form\Type\RemarqueType;
use AcMarche\Mercredi\Security\MercrediSecurity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Vich\UploaderBundle\Form\Type\VichImageType;

final class EnfantType extends AbstractType
{
    /**
     * @var string
     */
    private const REQUIRED = 'required';
    /**
     * @var string
     */
    private const LABEL = 'label';
    /**
     * @var string
     */
    private const PLACEHOLDER = 'placeholder';
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $year = new DateTime('today');
        $year = $year->format('Y');
        $isAdmin = !$this->security->isGranted(MercrediSecurity::ROLE_ADMIN);

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
                'birthday',
                BirthdayType::class,
                [
                    self::LABEL => 'Né le',
                    self::REQUIRED => $isAdmin,
                    'years' => range($year - 15, $year),
                ]
            )
            ->add(
                'sexe',
                ChoiceType::class,
                [
                    self::REQUIRED => $isAdmin,
                    'choices' => MercrediConstantes::SEXES,
                    self::PLACEHOLDER => 'Choisissez son sexe',
                ]
            )
            ->add(
                'ordre',
                OrdreType::class,
                [
                ]
            )
            ->add(
                'ecole',
                EntityType::class,
                [
                    'class' => Ecole::class,
                    self::REQUIRED => $isAdmin,
                    self::PLACEHOLDER => 'Choisissez son école',
                ]
            )
            ->add(
                'annee_scolaire',
                EntityType::class,
                [
                    'class' => AnneeScolaire::class,
                    self::LABEL => 'Année scolaire',
                    self::PLACEHOLDER => 'Choisissez son année scolaire',
                ]
            )
            ->add(
                'groupe_scolaire',
                EntityType::class,
                [
                    'class' => GroupeScolaire::class,
                    self::REQUIRED => false,
                    self::LABEL => 'Forcer le groupe scolaire',
                    self::PLACEHOLDER => 'Choisissez un groupe',
                    'help' => 'Utilisé pour le listing des présences',
                ]
            )
            ->add(
                'remarque',
                RemarqueType::class
            )
            ->add(
                'photoAutorisation',
                CheckboxType::class,
                [
                    self::REQUIRED => false,
                    self::LABEL => 'Autorisation de diffusion de ses photos',
                    'help' => 'Cochez si vous autorisez la diffusion des photos de l\'enfant',
                    'label_attr' => ['class' => 'switch-custom'],
                ]
            )
            ->add(
                'archived',
                CheckboxType::class,
                [
                    self::LABEL => 'Archiver',
                    'help' => 'Ces données seront toujours visibles, mais il ne pourra plus être inscrit nul part',
                    self::REQUIRED => false,
                    'label_attr' => ['class' => 'switch-custom'],
                ]
            )
            ->add(
                'accueilEcole',
                CheckboxType::class,
                [
                    self::LABEL => 'Accueils des écoles',
                    self::REQUIRED => false,
                    'help' => 'L\'enfant vient-il en accueil dans les écoles ?',
                    'label_attr' => ['class' => 'switch-custom'],
                ]
            )
            ->add(
                'photo',
                VichImageType::class,
                [
                    self::REQUIRED => false,
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => Enfant::class,
            ]
        );
    }
}
