<?php

namespace AcMarche\Mercredi\Facture\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FactureSendType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'from',
                EmailType::class,
                [
                    'label' => 'De',
                ]
            )
            ->add(
                'to',
                EmailType::class,
                [
                    'label' => 'A',
                ]
            )
            ->add(
                'sujet',
                TextType::class,
                [
                    'required' => true,
                ]
            )
            ->add(
                'texte',
                TextareaType::class,
                [
                    'required' => true,
                    'attr' => ['rows' => 10, 'cols' => 50],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
            ]
        );
    }
}
