<?php

namespace AcMarche\Mercredi\Document\Form;

use AcMarche\Mercredi\Entity\Document;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class DocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'nom',
                TextType::class,
                [
                    'required' => true,
                ]
            )
            ->add(
                'file',
                VichFileType::class,
                [
                    'label' => 'Fichier',
                    'help' => 'Uniquement images ou pdf',
                    'required' => false,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Document::class,
            ]
        );
    }
}
