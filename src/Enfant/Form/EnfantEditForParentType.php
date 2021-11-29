<?php

namespace AcMarche\Mercredi\Enfant\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

final class EnfantEditForParentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->remove('archived')
            ->remove('ordre')
            ->remove('groupe_scolaire');
    }

    public function getParent(): ?string
    {
        return EnfantType::class;
    }
}
