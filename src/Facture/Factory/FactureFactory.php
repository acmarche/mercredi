<?php

namespace AcMarche\Mercredi\Facture\Factory;

use AcMarche\Mercredi\Contrat\Facture\FacturePdfPresenceInterface;
use AcMarche\Mercredi\Facture\FactureInterface;
use Exception;
use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Pdf\PdfDownloaderTrait;
use DateTime;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

final class FactureFactory
{
    use PdfDownloaderTrait;

    private ParameterBagInterface $parameterBag;
    private FacturePdfPresenceInterface $facturePdfPresence;

    public function __construct(FacturePdfPresenceInterface $facturePdfPresence, ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
        $this->facturePdfPresence = $facturePdfPresence;
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
        $facture->setEcoles(implode(' ', $ecoles));
    }

    /**
     * @param array|Facture[] $factures
     * @throws Exception
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
            $htmlInvoice = $this->createHtml($facture);
            try {
                $this->getPdf()->generateFromHtml($htmlInvoice, $fileName);
            } catch (Exception $exception) {
                throw new Exception($exception->getMessage(), $exception->getCode(), $exception);
            }
            if ($i > $max) {
                return false;
            }
            $i++;
        }

        return true;
    }

    public function createHtml(FactureInterface $facture): string
    {
        return $this->facturePdfPresence->render($facture);
    }

    public function getBasePathFacture(string $month): string
    {
        return $this->parameterBag->get('kernel.project_dir').'/var/factures/'.$month.'/';
    }
}
