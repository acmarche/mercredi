<?php

namespace AcMarche\Mercredi\User\Form;

use AcMarche\Mercredi\Ecole\Repository\EcoleRepository;
use AcMarche\Mercredi\Entity\Scolaire\Ecole;
use AcMarche\Mercredi\User\Dto\AssociateUserEcoleDto;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AssociateEcoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'ecoles',
                EntityType::class,
                [
                    'label' => 'Sélectionnez une ou plusieurs écoles',
                    'class' => Ecole::class,
                    'placeholder' => 'Sélectionnez',
                    'required' => true,
                    'query_builder' => fn (EcoleRepository $cr) => $cr->findForAssociate(),
                    'multiple' => true,
                    'expanded' => true,
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => AssociateUserEcoleDto::class,
            ]
        );
    }
}
