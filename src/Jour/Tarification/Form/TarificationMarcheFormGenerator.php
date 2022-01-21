<?php

namespace AcMarche\Mercredi\Jour\Tarification\Form;

use AcMarche\Mercredi\Contrat\Tarification\TarificationFormGeneratorInterface;
use AcMarche\Mercredi\Entity\Jour;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Twig\Environment;

final class TarificationMarcheFormGenerator implements TarificationFormGeneratorInterface
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private Environment $environment
    ) {
    }

    public function generateForm(Jour $jour): FormInterface
    {
        return $this->formFactory->create(JourTarificationDegressiveType::class, $jour);
    }

    public function generateTarifsHtml(Jour $jour): string
    {
        return $this->environment->render(
            '@AcMarcheMercrediAdmin/jour/tarif/_detail_progressif.html.twig',
            [
                'jour' => $jour,
            ]
        );
    }
}
