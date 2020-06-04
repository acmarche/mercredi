<?php

namespace AcMarche\Mercredi\Pdf;

use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\Response;

class PdfGenerator
{
    /**
     * @var Pdf
     */
    private $pdf;

    public function __construct(Pdf $pdf)
    {
        $this->pdf = $pdf;
    }

    public function downloadPdf(string $html, string $fileName): Response
    {
        return new PdfResponse(
            $this->pdf->getOutputFromHtml($html),
            $fileName
        );
    }
}
