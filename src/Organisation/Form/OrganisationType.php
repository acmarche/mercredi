<?php

namespace AcMarche\Mercredi\Organisation\Form;

use AcMarche\Mercredi\Entity\Organisation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Iban;
use Vich\UploaderBundle\Form\Type\VichImageType;

final class OrganisationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add('nom')
            ->add('initiale')
            ->add('email')
            ->add('site_web')
            ->add('rue')
            ->add('code_postal')
            ->add('localite')
            ->add('telephone')
            ->add(
                'telephone_bureau',
                TextType::class,
                [
                    'required' => false,
                    'label' => 'Autre téléphone',
                ]
            )
            ->add('gsm')
            ->add('responsable_nom', TextType::class, [
                'required' => false,
                'label' => 'Nom du responsable',
            ])
            ->add('responsable_prenom', TextType::class, [
                'required' => false,
                'label' => 'Prénom du responsable',
            ])
            ->add('responsable_fonction', TextType::class, [
                'required' => false,
                'label' => 'Fonction du responsable',
            ])
            ->add('numero_compte', TextType::class, [
                'required' => true,
                'label' => 'Numéro de compte bancaire',
                'help' => 'Numéro de compte sur lequel seront versés les paiements pour les factures',
                'constraints' => [new Iban()],
            ])
            ->add('remarque')
            ->add(
                'photo',
                VichImageType::class,
                [
                    'required' => false,
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => Organisation::class,
            ]
        );
    }
}
