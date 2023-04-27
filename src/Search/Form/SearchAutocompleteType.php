<?php

namespace AcMarche\Mercredi\Search\Form;

use AcMarche\Mercredi\Form\Type\EnfantAutocompleteField;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

final class SearchAutocompleteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add('nom', EnfantAutocompleteField::class, [
                'label' => 'Sélectionnez un enfant',
            ]);
    }
}
