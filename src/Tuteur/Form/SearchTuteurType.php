<?php

namespace AcMarche\Mercredi\Tuteur\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;

final class SearchTuteurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'nom',
                SearchType::class,
                [
                    'required' => false,
                    'attr' => ['placeholder' => 'Nom', 'autocomplete' => 'off'],
                ]
            )
            ->add(
                'archived',
                CheckboxType::class,
                [
                    'label' => 'Afficher les parents archivés',
                    'required' => false,
                    'label_attr' => ['class' => 'switch-custom'],
                ]
            );
    }
}
