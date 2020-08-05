<?php

namespace AcMarche\Mercredi\User\Form;

use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Tuteur\Repository\TuteurRepository;
use AcMarche\Mercredi\User\Dto\AssociateUserTuteurDto;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AssociateParentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'tuteur',
                EntityType::class,
                [
                    'label' => 'Sélectionnez un parent',
                    'class' => Tuteur::class,
                    'placeholder' => 'Sélectionnez le parent',
                    'required' => true,
                    'query_builder' => function (TuteurRepository $cr) {
                        return $cr->findForAssociateParent();
                    },
                ]
            )
            ->add(
                'sendEmail',
                CheckboxType::class,
                [
                    'required' => false,
                    'label' => 'Prévenir par email le parent',
                    'help' => 'Un email va être envoyé au parent pour signaler que son compte a été associé à une fiche parent',
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => AssociateUserTuteurDto::class,
            ]
        );
    }
}
