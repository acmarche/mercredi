<?php

namespace AcMarche\Mercredi\Enfant\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class EnfantEditForParentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->remove('ordre')
            ->remove('groupe_scolaire');
    }

    public function getParent()
    {
        return EnfantType::class;
    }
}
