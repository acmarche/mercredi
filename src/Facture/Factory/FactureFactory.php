<?php

namespace AcMarche\Mercredi\Facture\Factory;

use AcMarche\Mercredi\Contrat\Facture\FactureRenderInterface;
use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Pdf\PdfDownloaderTrait;
use DateTime;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

final class FactureFactory
{
    use PdfDownloaderTrait;

    private FactureRenderInterface $factureRender;
    private ParameterBagInterface $parameterBag;

    public function __construct(FactureRenderInterface $factureRender, ParameterBagInterface $parameterBag)
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
    public function createAllPdf(array $factures, string $month, int $max = 30): bool
    {
        $path = $this->getBasePathFacture($month);
        $i = 0;
        foreach ($factures as $facture) {
            $fileName = $path.'facture-'.$facture->getId().'.pdf';
            if (is_readable($fileName)) {
                continue;
            }
            $htmlInvoice = $this->factureRender->renderForPdf($facture);
            $this->getPdf()->generateFromHtml($htmlInvoice, $fileName);
            if ($i > $max) {
                return false;
            }
            $i++;
        }

        return true;
    }

    public function getBasePathFacture(string $month): string
    {
        return $this->parameterBag->get('kernel.project_dir').'/var/factures/'.$month.'/';
    }

}
