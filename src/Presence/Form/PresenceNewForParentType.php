<?php

namespace AcMarche\Mercredi\Presence\Form;

use AcMarche\Mercredi\Ecole\Utils\EcoleUtils;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Presence\Dto\PresenceSelectDays;
use AcMarche\Mercredi\Presence\Repository\PresenceDaysProviderInterface;
use AcMarche\Mercredi\Utils\DateUtils;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PresenceNewForParentType extends AbstractType
{
    public function __construct(
        private PresenceDaysProviderInterface $presenceDaysProvider
    ) {
    }

    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $enfant = $formBuilder->getData()->getEnfant();

        $formBuilder
            ->add(
                'jours',
                EntityType::class,
                [
                    'class' => Jour::class,
                    'choices' => $this->presenceDaysProvider->getAllDaysToSubscribe($enfant),
                    'multiple' => true,
                    'label' => 'Sélectionnez une ou plusieurs dates',
                    'choice_label' => function (Jour $jour) {
                        $peda = '';
                        if ($jour->isPedagogique()) {
                            $ecoles = EcoleUtils::getNamesEcole($jour->getEcoles());
                            $peda = '(Pédagogique '.$ecoles.')';
                        }

                        return ucfirst(DateUtils::formatFr($jour->getDatejour()).' '.$peda);
                    },
                    'attr' => [
                        'style' => 'height:150px;',
                    ],
                    'group_by' => fn ($date) => $date->getDateJour()->format('m').'-'.$date->getDateJour()->format('Y'),
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => PresenceSelectDays::class,
            ]
        );
    }
}
