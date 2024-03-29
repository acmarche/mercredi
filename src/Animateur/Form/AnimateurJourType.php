<?php

namespace AcMarche\Mercredi\Animateur\Form;

use AcMarche\Mercredi\Ecole\Utils\EcoleUtils;
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
                    'query_builder' => fn (JourRepository $jourRepository) => $jourRepository->getQlNotPlaine(),
                    'group_by' => fn ($jour, $key, $id) => $jour->getDateJour()->format('Y'),
                    'required' => false,
                    'choice_label' => function (Jour $jour) {
                        $peda = '';
                        if ($jour->isPedagogique()) {
                            $ecoles = EcoleUtils::getNamesEcole($jour->getEcoles());
                            $peda = '(Pédagogique '.$ecoles.')';
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
