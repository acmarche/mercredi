<?php

namespace AcMarche\Mercredi\Facture\Factory;

use AcMarche\Mercredi\Contrat\Facture\FacturePdfPlaineInterface;
use AcMarche\Mercredi\Contrat\Facture\FacturePdfPresenceInterface;
use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Facture\FactureInterface;
use AcMarche\Mercredi\Facture\Repository\FactureRepository;
use AcMarche\Mercredi\Pdf\PdfDownloaderTrait;
use DateTime;
use Exception;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class FactureFactory
{
    use PdfDownloaderTrait;

    public function __construct(
        #[Autowire('kernel.project_dir')]
        private string $project_dir,
        private FacturePdfPresenceInterface $facturePdfPresence,
        private FactureRepository $factureRepository,
        private FacturePdfPlaineInterface $facturePdfPlaine,
    ) {
    }

    public function newInstance(Tuteur $tuteur, ?Plaine $plaine = null): Facture
    {
        $facture = null;
        if ($plaine) {
            $facture = $this->factureRepository->findByTuteurAndPlaine($tuteur, $plaine);

            if (!$facture) {
                $facture = new Facture($tuteur);
                $facture->setPlaine($plaine);
            }
        }
        if (!$facture) {
            $facture = new Facture($tuteur);
        }
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
     *
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
            ++$i;
        }

        return true;
    }


    /**
     * @throws Exception
     */
    public function createOnePdf(Facture $facture, string $month, bool $force = false): ?string
    {
        $path = $this->getBasePathFacture($month);
        $fileName = $path.'facture-'.$facture->getId().'.pdf';
        if ($force === false && is_readable($fileName)) {
            return $fileName;
        }
        if (is_readable($fileName)) {
            try {
                unlink($fileName);
            } catch (Exception $exception) {
                throw new Exception($exception->getMessage(), $exception->getCode(), $exception);
            }
        }
        $htmlInvoice = $this->createHtml($facture);
        try {
            $this->getPdf()->generateFromHtml($htmlInvoice, $fileName);

            return $fileName;
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    public function createHtml(FactureInterface $facture): string
    {
        if ($facture->getPlaineNom()) {
            $html = $this->facturePdfPlaine->render($facture);
        } else {
            $html = $this->facturePdfPresence->render($facture);
        }

        return $html;
    }

    public function getBasePathFacture(string $month): string
    {
        return $this->project_dir.'/var/factures/'.$month.'/';
    }
}
