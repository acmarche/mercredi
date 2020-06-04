<?php

namespace AcMarche\Mercredi\Tuteur\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConjointType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'relation_conjoint',
                TextType::class,
                [
                    'label' => 'Relation entre les conjoints',
                    'required' => false,
                    'help' => 'Papa, Belle-mère, Maman, Oncle...',
                ]
            )
            ->add(
                'nom_conjoint',
                TextType::class,
                [
                    'label' => 'Nom',
                    'required' => false,
                ]
            )
            ->add(
                'prenom_conjoint',
                TextType::class,
                [
                    'label' => 'Prénom',
                    'required' => false,
                ]
            )
            ->add(
                'email_conjoint',
                EmailType::class,
                [
                    'required' => false,
                    'label' => 'Email',
                ]
            )
            ->add(
                'telephone_conjoint',
                TextType::class,
                [
                    'required' => false,
                    'label' => 'Téléphone',
                ]
            )
            ->add(
                'telephone_bureau_conjoint',
                TextType::class,
                [
                    'required' => false,
                    'label' => 'Téléphone du bureau',
                ]
            )
            ->add(
                'gsm_conjoint',
                TextType::class,
                [
                    'label' => 'Gsm',
                    'required' => false,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'inherit_data' => true,
            ]
        );
    }
}
