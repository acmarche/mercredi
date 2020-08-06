<?php

namespace AcMarche\Mercredi\Facture\Factory;

use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Pdf\PdfDownloaderTrait;
use Symfony\Component\HttpFoundation\Response;

final class FacturePdfFactoryTrait
{
    use PdfDownloaderTrait;

    /**
     * @var FactureFactory
     */
    private $factureFactory;

    public function __construct(FactureFactory $factureFactory)
    {
        $this->factureFactory = $factureFactory;
    }

    public function generate(Facture $facture): Response
    {
        $date = $facture->getFactureLe();
        $html = $this->factureFactory->generateFullHtml($facture);

        // return new Response($html);

        return $this->downloadPdf($html, 'facture_'.$date->format('d-m-Y').'.pdf');
    }
}
