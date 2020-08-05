<?php

namespace AcMarche\Mercredi\Message\Form;

use AcMarche\Mercredi\Entity\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageType extends AbstractType
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
            )
            ->add(
                'file',
                FileType::class,
                [
                    'label' => 'Pièce jointe',
                    'required' => false,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Message::class,
            ]
        );
    }
}
