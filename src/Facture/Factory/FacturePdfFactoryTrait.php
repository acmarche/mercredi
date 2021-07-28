<?php

namespace AcMarche\Mercredi\Facture\Factory;

use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Facture\Render\FactureRender;
use AcMarche\Mercredi\Pdf\PdfDownloaderTrait;
use Symfony\Component\HttpFoundation\Response;

final class FacturePdfFactoryTrait
{
    use PdfDownloaderTrait;

    private FactureRender $factureRender;

    public function __construct(FactureRender $factureRender)
    {
        $this->factureRender = $factureRender;
    }

    public function generate(Facture $facture): Response
    {
        $date = $facture->getFactureLe();
        $html = $this->factureRender->generateOneHtml($facture);

        //   return new Response($html);

        return $this->downloadPdf($html, 'facture_'.$date->format('d-m-Y').'.pdf');
    }

    /**
     * @param array|Facture[] $factures
     * @param string $month
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function generates(array $factures, string $month): Response
    {
        $html = $this->factureRender->generateMultipleHtml($factures);

        //  return new Response($html);

        return $this->downloadPdf($html, 'factures_'.$month.'.pdf');
    }
}
