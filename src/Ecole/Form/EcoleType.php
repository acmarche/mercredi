<?php

namespace AcMarche\Mercredi\Ecole\Form;

use AcMarche\Mercredi\Entity\Ecole;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class EcoleType extends AbstractType
{
    /**
     * @var string
     */
    private const REQUIRED = 'required';
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add('nom')
            ->add(
                'rue',
                TextType::class,
                [
                    self::REQUIRED => false,
                ]
            )
            ->add(
                'code_postal',
                IntegerType::class,
                [
                    self::REQUIRED => false,
                ]
            )
            ->add(
                'localite',
                TextType::class,
                [
                    self::REQUIRED => false,
                ]
            )
            ->add('telephone')
            ->add('gsm')
            ->add(
                'email',
                EmailType::class,
                [
                    self::REQUIRED => false,
                ]
            )
            ->add(
                'remarque',
                TextareaType::class,
                [
                    self::REQUIRED => false,
                    'attr' => ['rows' => 8],
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => Ecole::class,
            ]
        );
    }
}
