<?php

namespace AcMarche\Mercredi\Form;

use AcMarche\Mercredi\Entity\Ecole;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EcoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('rue')
            ->add('code_postal')
            ->add('localite')
            ->add('telephone')
            ->add('gsm')
            ->add('email')
            ->add('remarque')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ecole::class,
        ]);
    }
}
