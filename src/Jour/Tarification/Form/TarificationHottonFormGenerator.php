<?php

namespace AcMarche\Mercredi\Jour\Tarification\Form;

use AcMarche\Mercredi\Entity\Jour;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Twig\Environment;

class TarificationHottonFormGenerator implements TarificationFormGeneratorInterface
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;
    /**
     * @var Environment
     */
    private $environment;

    public function __construct(FormFactoryInterface $formFactory, Environment $environment)
    {
        $this->formFactory = $formFactory;
        $this->environment = $environment;
    }

    public function generateForm(Jour $jour): FormInterface
    {
        if ($jour->isPedagogique()) {
            return $this->generateFullDayFormType($jour);
        }

        return $this->generateDegressifFormType($jour);
    }

    private function generateDegressifFormType(Jour $jour): FormInterface
    {
        return $this->formFactory->create(JourTarificationDegressiveWithForfaitType::class, $jour);
    }

    private function generateFullDayFormType(Jour $jour): FormInterface
    {
        return $this->formFactory->create(JourTarificationFullDayType::class, $jour);
    }

    public function generateTarifsHtml(Jour $jour): string
    {
        if ($jour->isPedagogique()) {
            return $this->environment->render(
                '@AcMarcheMercrediAdmin/jour/tarif/_detail_full_day.html.twig',
                [
                    'jour' => $jour,
                ]
            );
        }

        return $this->environment->render(
            '@AcMarcheMercrediAdmin/jour/tarif/_detail_progressif_forfait.html.twig',
            [
                'jour' => $jour,
            ]
        );
    }

}
