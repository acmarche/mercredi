<?php


namespace AcMarche\Mercredi\Facture\Form;

use AcMarche\Mercredi\Entity\Facture\Facture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FactureEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'factureLe',
                DateType::class,
                [
                    'label' => 'Date de la facture',
                    'widget' => 'single_text',
                    'required' => true,
                    'attr' => ['autocomplete' => 'off'],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Facture::class,
            ]
        );
    }

}
