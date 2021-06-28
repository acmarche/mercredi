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
    public $pdf;

    /**
     * @required
     */
    public function setPdf(Pdf $pdf): void
    {
        $this->pdf = $pdf;
    }

    /**
     * @return Pdf
     */
    public function getPdf(): Pdf
    {
        return $this->pdf;
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
