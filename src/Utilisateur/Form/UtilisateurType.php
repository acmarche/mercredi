<?php

namespace AcMarche\Mercredi\Utilisateur\Form;

use AcMarche\Mercredi\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UtilisateurType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'nom',
                TextType::class,
                array(
                    'required' => true,
                )
            )
            ->add(
                'prenom',
                TextType::class,
                array(
                    'required' => true,
                )
            )
            ->add('email', EmailType::class)
            ->add('username', TextType::class)
            ->add(
                'plainPassword',
                RepeatedType::class,
                array(
                    'type' => PasswordType::class,
                    'options' => array(
                        'attr' => array(
                            'autocomplete' => 'new-password',
                        ),
                    ),
                    'first_options' => array('label' => 'Mot de passe'),
                    'second_options' => array('label' => 'Répéter le mot de passe'),
                )
            );
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
