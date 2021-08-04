<?php

namespace AcMarche\Mercredi\Plaine\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichFileType;

final class PlaineGroupeEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->remove('groupeScolaire')
            ->add(
                'file',
                VichFileType::class,
                [
                    'label' => 'Fichier',
                    'help' => 'Uniquement images ou pdf. Fichier qui sera envoyé aux parents pour les modalités',
                    'required' => false,
                ]
            );
    }

    public function getParent()
    {
        return PlaineGroupeType::class;
    }
}
