<?php

namespace AcMarche\Mercredi\Plaine\Form;

use AcMarche\Mercredi\Entity\Plaine;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlaineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add(
                'remarque',
                TextareaType::class,
                [
                    'required' => false,
                    'attr' => ['rows' => 8],
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Plaine::class,
        ]);
    }
}
