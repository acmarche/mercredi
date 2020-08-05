<?php

namespace AcMarche\Mercredi\Organisation\Form;

use AcMarche\Mercredi\Entity\Organisation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class OrganisationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('initiale')
            ->add('email')
            ->add('site_web')
            ->add('rue')
            ->add('code_postal')
            ->add('localite')
            ->add('telephone')
            ->add(
                'telephone_bureau',
                TextType::class,
                [
                    'required' => false,
                    'label' => 'Autre téléphone',
                ]
            )
            ->add('gsm')
            ->add('remarque')
            ->add(
                'photo',
                VichImageType::class,
                [
                    'required' => false,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Organisation::class,
            ]
        );
    }
}
