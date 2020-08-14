<?php

namespace AcMarche\Mercredi\Animateur\Form;

use AcMarche\Mercredi\Entity\Animateur;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Jour\Repository\JourRepository;
use AcMarche\Mercredi\Utils\DateUtils;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AnimateurJourType extends AbstractType
{

    public function buildForm(FormBuilderInterface $formBuilder, array $options): void
    {
        $formBuilder
            ->add(
                'jours',
                EntityType::class,
                [
                    'class' => Jour::class,
                    'placeholder' => 'Jours d\'accueil',
                    'query_builder' => function (JourRepository $jourRepository) {
                        return $jourRepository->getQbForListing();
                    },
                    'group_by' => function ($jour, $key, $id) {
                        return $jour->getDateJour()->format('Y');
                    },
                    'required' => false,
                    'choice_label' => function (Jour $jour) {
                        $peda = '';
                        if ($jour->isPedagogique()) {
                            $peda = '(P)';
                        }

                        return ucfirst(DateUtils::formatFr($jour->getDatejour()).' '.$peda);
                    },
                    'multiple' => true,
                    'expanded' => true,
                ]
            );
    }

    public function configureOptions(OptionsResolver $optionsResolver): void
    {
        $optionsResolver->setDefaults(
            [
                'data_class' => Animateur::class,
            ]
        );
    }
}
