<?php

namespace AcMarche\Mercredi\Jour\Tarification\Form;

use AcMarche\Mercredi\Entity\Jour;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class JourTarificationDegressiveType extends AbstractType
{
    /**
     * @var string
     */
    private const REQUIRED = 'required';
    /**
     * @var string
     */
    private const LABEL = 'label';
    /**
     * @var string
     */
    private const HELP = 'help';

    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'prix1',
                MoneyType::class,
                [
                    self::REQUIRED => true,
                    self::LABEL => 'Prix 1er enfant',
                    self::HELP => 'Uniquement les chiffres',
                ]
            )
            ->add(
                'prix2',
                MoneyType::class,
                [
                    self::REQUIRED => true,
                    self::LABEL => 'Prix 2iem enfant',
                    self::HELP => 'Uniquement les chiffres',
                ]
            )
            ->add(
                'prix3',
                MoneyType::class,
                [
                    self::REQUIRED => true,
                    self::LABEL => 'Prix des suivants',
                    self::HELP => 'Uniquement les chiffres',
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => Jour::class,
            ]
        );
    }
}
