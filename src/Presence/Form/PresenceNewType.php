<?php

namespace AcMarche\Mercredi\Presence\Form;

use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Jour\Repository\JourRepository;
use AcMarche\Mercredi\Presence\Dto\PresenceSelectDays;
use AcMarche\Mercredi\Utils\DateUtils;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PresenceNewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $enfant = $builder->getData()->getEnfant();

        $builder
            ->add(
                'jours',
                EntityType::class,
                [
                    'class' => Jour::class,
                    'multiple' => true,
                    'query_builder' => function (JourRepository $cr) use ($enfant) {
                        return $cr->getQbDaysNotRegisteredByEnfant($enfant);
                    },
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

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => PresenceSelectDays::class,
            ]
        );
    }
}
