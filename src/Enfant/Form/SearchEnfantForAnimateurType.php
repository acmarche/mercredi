<?php


namespace AcMarche\Mercredi\Enfant\Form;

use AcMarche\Mercredi\Entity\Animateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchEnfantForAnimateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $animateur = $options['animateur'];
        $formBuilder
            ->add(
                'nom',
                SearchType::class,
                [
                    'required' => false,
                    'attr' => ['placeholder' => 'Nom'],
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            array(
                'animateur' => null,
            )
        )
            ->setAllowedTypes('animateur', Animateur::class)
            ->setRequired('animateur');
    }
}
