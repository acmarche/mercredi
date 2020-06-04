<?php

namespace AcMarche\Mercredi\User\Form;

use AcMarche\Mercredi\Entity\User;
use AcMarche\Mercredi\Security\MercrediSecurity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $roles = MercrediSecurity::ROLES;
        $builder
            ->remove('plainPassword')
            ->add(
                'roles',
                ChoiceType::class,
                [
                    'choices' => array_combine($roles, $roles),
                    'multiple' => true,
                    'expanded' => true,
                ]
            );
    }

    public function getParent()
    {
        return UserType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => User::class,
            ]
        );
    }
}
