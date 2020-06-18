<?php

namespace AcMarche\Mercredi\Contact\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'nom',
                TextType::class,
                [
                    'label' => 'Votre nom',
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'attr' => ['Votre email'],
                ]
            )
            ->add(
                'texte',
                TextareaType::class,
                [
                    'attr' => ['rows' => 5],
                ]
            );
    }
}
