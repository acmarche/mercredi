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

final class MessageType extends AbstractType
{
    /**
     * @var string
     */
    private const REQUIRED = 'required';
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
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
                    self::REQUIRED => true,
                ]
            )
            ->add(
                'texte',
                TextareaType::class,
                [
                    self::REQUIRED => true,
                    'attr' => ['rows' => 10, 'cols' => 50],
                ]
            )
            ->add(
                'file',
                FileType::class,
                [
                    'label' => 'Pièce jointe',
                    self::REQUIRED => false,
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => Message::class,
            ]
        );
    }
}
