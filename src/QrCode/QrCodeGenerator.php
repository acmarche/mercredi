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

        $data = $this->executeRequest($this->base_uri, ['query' => $params]);

        $path = $this->parameterBag->get('kernel.project_dir').$facture->getUuid().'.png';
        $filesystem = new Filesystem();
        $filesystem->dumpFile($path, $data);

        return $path;
    }
}