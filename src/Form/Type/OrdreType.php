<?php


namespace AcMarche\Mercredi\Form\Type;


use AcMarche\Mercredi\Data\MercrediConstantes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrdreType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'required' => false,
                'choices' => MercrediConstantes::ORDRES,
            ]
        );
    }

    public function getParent()
    {
        return ChoiceType::class;
    }

}
