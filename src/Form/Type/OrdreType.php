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
                'required' => true,
                'choices' => MercrediConstantes::ORDRES,
                'attr' => ['class' => 'custom-select my-1 mr-sm-2'],
            ]
        );
    }

    public function getParent()
    {
        return ChoiceType::class;
    }

}
