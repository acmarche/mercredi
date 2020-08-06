<?php

namespace AcMarche\Mercredi\Facture\Factory;

use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Entity\Tuteur;
use DateTime;
use Twig\Environment;

final class FactureFactory
{
    /**
     * @var Environment
     */
    private $environment;

    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
    }

    public function newInstance(Tuteur $tuteur): Facture
    {
        $facture = new Facture($tuteur);
        $facture->setFactureLe(new DateTime());
        $facture->setNom($tuteur->getNom());
        $facture->setPrenom($tuteur->getPrenom());
        $facture->setRue($tuteur->getRue());
        $facture->setCodePostal($tuteur->getCodePostal());
        $facture->setLocalite($tuteur->getLocalite());

        return $facture;
    }

    public function generateFullHtml(Facture $facture): string
    {
        $tuteur = $facture->getTuteur();

        return $this->environment->render(
            '@AcMarcheMercrediAdmin/facture/pdf/index.html.twig',
            [
                'facture' => $facture,
                'tuteur' => $tuteur,
            ]
        );
    }

    public function generateHtml(Facture $facture): string
    {
        $tuteur = $facture->getTuteur();

        return $this->environment->render(
            '@AcMarcheMercrediAdmin/facture/_facture.html.twig',
            [
                'facture' => $facture,
                'tuteur' => $tuteur,
            ]
        );
    }
}
