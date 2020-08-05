<?php

namespace AcMarche\Mercredi\Presence\Form;

use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Presence\Dto\PresenceSelectDays;
use AcMarche\Mercredi\Presence\Repository\PresenceDaysProviderInterface;
use AcMarche\Mercredi\Utils\DateUtils;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PresenceNewForParentType extends AbstractType
{
    /**
     * @var PresenceDaysProviderInterface
     */
    private $presenceDaysProvider;

    public function __construct(PresenceDaysProviderInterface $presenceDaysProvider)
    {
        $this->presenceDaysProvider = $presenceDaysProvider;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $enfant = $builder->getData()->getEnfant();

        $builder
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
                            $peda = '(P)';
                        }

                        return ucfirst(DateUtils::formatFr($jour->getDatejour()).' '.$peda);
                    },
                    'attr' => ['style' => 'height:150px;'],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => PresenceSelectDays::class,
            ]
        );
    }
}
