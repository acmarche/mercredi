<?php

namespace AcMarche\Mercredi\QrCode;

use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Http\ConnectionTrait;
use AcMarche\Mercredi\Organisation\Traits\OrganisationPropertyInitTrait;
use Knp\DoctrineBehaviors\Exception\ShouldNotHappenException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;

class QrCodeGenerator
{
    use OrganisationPropertyInitTrait;
    use ConnectionTrait;

    public function __construct(private ParameterBagInterface $parameterBag)
    {
    }

    /**
     * @throws ShouldNotHappenException
     * @throws \Exception
     */
    public function generate(Facture $facture, float $amount): string
    {
        $this->connect();
        $params = [
            'bname' => $this->organisation->getNom(),
            'iban' => $this->organisation->numero_compte,
            'euro' => $amount,
            'info' => $facture->getCommunication(),
        ];

        $url = 'https://epc-qr.eu/?for=Hotton&euro='.$amount.'&pp=maco';
        $data = $this->executeRequest($url);
       // $data = $this->executeRequest($this->base_uri, ['query' => $params]);

        $fileName = DIRECTORY_SEPARATOR.'qrcode'.DIRECTORY_SEPARATOR.$facture->getUuid().'.png';
        $filePath = DIRECTORY_SEPARATOR.'public'.$fileName;

        $imageFullPath = $this->parameterBag->get('kernel.project_dir').$filePath;
        try {
            $filesystem = new Filesystem();
            $filesystem->dumpFile($imageFullPath, $data);
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }

        $mime = \mime_content_type($imageFullPath);
        if ($mime != 'image/png') {
            throw new \Exception($data);
        }

        return $fileName;
    }
}