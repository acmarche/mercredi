<?php

namespace AcMarche\Mercredi\Sante\Form;

use AcMarche\Mercredi\Entity\Sante\SanteQuestion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SanteQuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add(
                'complement',
                CheckboxType::class,
                [
                    'label' => 'Un complément d\'information est-il nécessaire ?',
                    'help' => 'Si oui cochez la case',
                    'required' => false,
                    'label_attr'=> ['class' => 'switch-custom']
                ]
            )
            ->add(
                'complementLabel',
                TextType::class,
                [
                    'label' => 'Texte d\'aide pour le complément',
                    'help' => 'Par ex: Date de vaccin, quel type de médicaments...',
                    'required' => false,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => SanteQuestion::class,
            ]
        );
    }
}
