<?php

namespace AcMarche\Mercredi\Facture\Factory;

use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Pdf\AbstractPdfDownloader;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class FacturePdfFactory extends AbstractPdfDownloader
{
    /**
     * @var Environment
     */
    private $environment;
    /**
     * @var FactureFactory
     */
    private $factureFactory;

    public function __construct(Pdf $pdf, FactureFactory $factureFactory)
    {
        parent::__construct($pdf);
        $this->factureFactory = $factureFactory;
    }

    public function generate(Facture $facture): Response
    {
        $date = $facture->getFactureLe();
        $html = $this->factureFactory->generateHtml($facture);

        // return new Response($html);

        return $this->downloadPdf($html, 'facture_'.$date->format('d-m-Y').'.pdf');
    }
}
