<?php


namespace AcMarche\Mercredi\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArchivedType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'required' => false,
                'label' => 'Archiver',
            ]
        );
    }

    public function getParent()
    {
        return CheckboxType::class;
    }

}
