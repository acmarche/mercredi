<?php

namespace AcMarche\Mercredi\Pdf;

use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\Response;

trait PdfDownloaderTrait
{
    /**
     * @var Pdf
     */
    private $pdf;

    /**
     * @param Pdf $pdf
     * @required
     */
    public function setPdf(Pdf $pdf)
    {
        $this->pdf = $pdf;
    }

    public function downloadPdf(string $html, string $fileName): Response
    {
        //debug
        // return new Response($html);

        return new PdfResponse(
            $this->pdf->getOutputFromHtml($html),
            $fileName
        );
    }
}
