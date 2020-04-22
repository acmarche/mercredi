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
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $roles = MercrediSecurity::ROLES;
        $builder
            ->remove("plainPassword")
            ->add(
                "roles",
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

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => User::class,
            )
        );
    }
}
