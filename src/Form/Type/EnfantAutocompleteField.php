<?php

namespace AcMarche\Mercredi\Form\Type;

use AcMarche\Mercredi\Entity\Enfant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\ParentEntityAutocompleteType;

#[AsEntityAutocompleteField]
class EnfantAutocompleteField extends AbstractType
{
    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => Enfant::class,
            'label' => 'Enfant',
            'multiple' => false,
            'autocomplete_url' => $this->urlGenerator->generate('mercredi_admin_autocomplete_enfants'),
        ]);
    }

    public function getParent(): string
    {
        return ParentEntityAutocompleteType::class;
    }
}