<?php

namespace AcMarche\Mercredi\Facture\Factory;

use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Pdf\PdfDownloaderTrait;
use Symfony\Component\HttpFoundation\Response;

final class FacturePdfFactoryTrait
{
    use PdfDownloaderTrait;

    private FactureFactory $factureFactory;

    public function __construct(FactureFactory $factureFactory)
    {
        $this->factureFactory = $factureFactory;
    }

    public function generate(Facture $facture): Response
    {
        $date = $facture->getFactureLe();
        $html = $this->factureFactory->generateOneHtml($facture);

        //  return new Response($html);

        return $this->downloadPdf($html, 'facture_'.$date->format('d-m-Y').'.pdf');
    }

    /**
     * @param array|Facture[] $factures
     * @param string $month
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function generates(array $factures, string $month): Response
    {
        $html = $this->factureFactory->generateMultipleHtml($factures);

        //  return new Response($html);

        return $this->downloadPdf($html, 'factures_'.$month.'.pdf');
    }
}
