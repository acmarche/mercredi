<?php

namespace AcMarche\Mercredi\Parent\Form;

use AcMarche\Mercredi\Admin\Entity\Sante\SanteFiche;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SanteFicheType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'personneUrgence',
                TextareaType::class,
                [
                    'label' => 'Personne(s) en cas d\'urgence',
                    'help' => 'Nom, prénom et numéro de téléphone',
                ]
            )
            ->add(
                'medecinNom',
                TextType::class,
                ['label' => 'Nom du médecin']
            )
            ->add(
                'medecinTelephone',
                TextType::class,
                ['label' => 'Téléphone du médecin']
            )
            ->add(
                'remarques',
                TextareaType::class,
                [
                    'required' => false,
                    'label' => "D'autres remarques",
                ]
            )
            ->add(
                'questions',
                CollectionType::class,
                [
                    'entry_type' => SanteReponseType::class,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => SanteFiche::class,
            ]
        );
    }
}
