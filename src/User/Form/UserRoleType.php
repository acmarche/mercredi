<?php

namespace AcMarche\Mercredi\User\Form;

use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Security\MercrediSecurity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserRoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $roles = array_flip(MercrediSecurity::ROLES);
        $builder
            ->add('roles',
                ChoiceType::class,
                [
                    'choices' => $roles,
                    'multiple' => true,
                    'expanded' => true,
                ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => User::class,
            ]
        );
    }
}
