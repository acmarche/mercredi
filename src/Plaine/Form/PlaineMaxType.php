<?php

namespace AcMarche\Mercredi\Plaine\Form;

use AcMarche\Mercredi\Entity\Plaine\PlaineMax;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlaineMaxType extends AbstractType
{
    protected $label = false;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'groupe',
                TextType::class,
                [
                    'attr' => ['readonly' => true],
                ]
            )
            ->add('maximum', IntegerType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => PlaineMax::class,
            ]
        );
    }
}
