<?php

namespace AcMarche\Mercredi\Page\Form;

use AcMarche\Mercredi\Entity\Page;
use AcMarche\Mercredi\Form\Type\ArchivedType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'nom',
                DateType::class,
                [
                    'label' => 'Date du page de garde',
                    'widget' => 'single_text',
                    'required' => true,
                    'attr' => ['autocomplete' => 'off'],
                ]
            )
            ->add(
                'content',
                CKEditorType::class,
                [
                    'required' => true,
                    'label' => 'Contenu',
                    'attr' => ['cols' => 5],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Page::class,
            ]
        );
    }
}
