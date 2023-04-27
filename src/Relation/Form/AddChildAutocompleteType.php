<?php

namespace AcMarche\Mercredi\Relation\Form;

use AcMarche\Mercredi\Form\Type\EnfantAutocompleteField;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AddChildAutocompleteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add('nom', EnfantAutocompleteField::class, [
                'label' => 'Sélectionnez un enfant',
            ]);
    }
}
