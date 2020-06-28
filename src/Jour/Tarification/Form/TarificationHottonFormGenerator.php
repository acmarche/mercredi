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

    public function generate(Jour $jour): FormInterface
    {
        if ($jour->isPedagogique()) {
            return $this->generateFullDayType($jour);
        }

        return $this->generateDegressifType($jour);
    }

    private function generateDegressifType(Jour $jour): FormInterface
    {
        return $this->formFactory->create(JourTarificationDegressiveType::class, $jour);
    }

    private function generateFullDayType(Jour $jour): FormInterface
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
            '@AcMarcheMercrediAdmin/jour/tarif/_detail_progressif.html.twig',
            ['jour' => $jour]
        );
    }

}
