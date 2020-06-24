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

    public function __construct(Pdf $pdf, Environment $environment)
    {
        parent::__construct($pdf);
        $this->environment = $environment;
    }

    public function generate(Facture $facture): Response
    {
        $date = $facture->getFactureLe();
        $html = '';

        return new Response($html);

        return $this->downloadPdf($html, 'facture_'.$date->format('d-m-Y').'.pdf');
    }
}
