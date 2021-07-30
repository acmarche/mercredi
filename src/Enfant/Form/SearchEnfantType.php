<?php

namespace AcMarche\Mercredi\Enfant\Form;

use AcMarche\Mercredi\Entity\AnneeScolaire;
use AcMarche\Mercredi\Entity\Ecole;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;

final class SearchEnfantType extends AbstractType
{
    /**
     * @var string
     */
    private const REQUIRED = 'required';
    /**
     * @var string
     */
    private const ATTR = 'attr';
    /**
     * @var string
     */
    private const PLACEHOLDER = 'placeholder';

    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'nom',
                SearchType::class,
                [
                    'required' => false,
                    self::ATTR => [self::PLACEHOLDER => 'Nom', 'autocomplete' => 'off'],
                ]
            )
            ->add(
                'ecole',
                EntityType::class,
                [
                    'class' => Ecole::class,
                    'required' => false,
                    self::PLACEHOLDER => 'Choisissez une école',
                    self::ATTR => ['class' => 'custom-select my-1 mr-sm-2'],
                ]
            )
            ->add(
                'annee_scolaire',
                EntityType::class,
                [
                    'class' => AnneeScolaire::class,
                    'label' => 'Année scolaire',
                    self::PLACEHOLDER => 'Choisissez son année scolaire',
                    self::ATTR => ['class' => 'custom-select my-1 mr-sm-2'],
                    'required' => false,
                ]
            );
    }
}
