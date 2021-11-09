<?php

namespace AcMarche\Mercredi\Facture\Factory;

use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Facture\Render\FactureRender;
use AcMarche\Mercredi\Pdf\PdfDownloaderTrait;
use DateTime;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

final class FactureFactory
{
    use PdfDownloaderTrait;

    private FactureRender $factureRender;
    private ParameterBagInterface $parameterBag;

    public function __construct(FactureRender $factureRender, ParameterBagInterface $parameterBag)
    {
        $this->factureRender = $factureRender;
        $this->parameterBag = $parameterBag;
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

    public function setEcoles(Facture $facture): void
    {
        $ecoles = array_unique($facture->ecolesListing);
        $facture->setEcoles(join(' ', $ecoles));
    }

    /**
     * @param array|Facture[] $factures
     */
    public function createAllPdf(array $factures)
    {
        $path = $this->parameterBag->get('kernel.project_dir').'/var/factures/';
        foreach ($factures as $facture) {
            $fileName = $path.'facture-'.$facture->getId().'.pdf';
            if (is_readable($fileName)) {
                continue;
            }
            $htmlInvoice = $this->factureRender->generateOneHtml($facture);
            $this->getPdf()->generateFromHtml($htmlInvoice, $fileName);
        }
    }

}
