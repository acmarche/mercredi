<?php

namespace AcMarche\Mercredi\QrCode;

use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Facture\Utils\BelgianStructuredGenerator;
use AcMarche\Mercredi\Organisation\Traits\OrganisationPropertyInitTrait;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Writer\PngWriter;
use Knp\DoctrineBehaviors\Exception\ShouldNotHappenException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class QrCodeGenerator
{
    use OrganisationPropertyInitTrait;

    public function __construct(
        #[Autowire('%kernel.project_dir%')]
        private string $project_dir,
    ) {}

    /**
     * Service Tag:    BCD
     * Version:    002
     * Character set:    1
     * Identification:    SCT
     * Name:    Red Cross
     * IBAN:    BE72000000001616
     * Amount:    EUR1
     * Reason (4 chars max):    CHAR
     * Ref of invoice:    Empty line or REFINVOICE
     * Or text:    Urgency fund or Empty line
     * Information:    Sample EPC QR code
     * @throws ShouldNotHappenException
     * @throws \Exception
     */
    public function generate(Facture $facture, float $amount): string
    {
        $qr_content = [];
        $qr_content[] = "BCD";
        $qr_content[] = "002";
        $qr_content[] = "1";
        $qr_content[] = "SCT";
        $qr_content[] = "";//BIC
        $qr_content[] = $this->organisation->getNom();
        $qr_content[] = $this->organisation->numero_compte;
        $qr_content[] = "EUR".$amount;
        $qr_content[] = "CHAR";//reason
        $qr_content[] = "";
        $qr_content[] = $facture->getCommunication();//BelgianStructuredGenerator::generate();
        $qr_content[] = "Sample EPC QR code";

        $qr_string = implode(PHP_EOL, $qr_content);

        $qrCode = new Builder(
            writer: new PngWriter(),
            writerOptions: [],
            validateResult: false,//todo active !
            data: $qr_string,
            encoding: new Encoding('UTF-8'),
            size: 300,
        );
        $result = $qrCode->build();

        $fileName = $facture->getUuid().'.png';
        $directory = $this->project_dir.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'qrcode'.DIRECTORY_SEPARATOR;
        $publicPath = DIRECTORY_SEPARATOR.'qrcode'.DIRECTORY_SEPARATOR.$fileName;

        try {
            $result->saveToFile($directory.$fileName);
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }

        $mime = \mime_content_type($directory.$fileName);
        if ($mime != 'image/png') {
            throw new \Exception('Not image/png mime:'.$result->getMimeType());
        }

        return $publicPath;
    }
}