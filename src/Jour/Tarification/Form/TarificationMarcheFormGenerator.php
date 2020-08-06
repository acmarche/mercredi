<?php

namespace AcMarche\Mercredi\Jour\Tarification\Form;

use AcMarche\Mercredi\Entity\Jour;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Twig\Environment;

final class TarificationMarcheFormGenerator implements TarificationFormGeneratorInterface
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
        return $this->formFactory->create(JourTarificationDegressiveType::class, $jour);
    }

    public function generateTarifsHtml(Jour $jour): string
    {
        return $this->environment->render(
            '@AcMarcheMercrediAdmin/jour/tarif/_detail_progressif.html.twig',
            ['jour' => $jour]
        );
    }
}
