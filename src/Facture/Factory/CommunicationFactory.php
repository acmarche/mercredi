<?php


namespace AcMarche\Mercredi\Facture\Factory;


use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Facture\Repository\FactureRepository;

class CommunicationFactory
{
    private FactureRepository $factureRepository;

    public function __construct(FactureRepository $factureRepository)
    {
        $this->factureRepository = $factureRepository;
    }

    //085 / 1927 / 54115
    public function generate(Facture $facture): string
    {
        $id = rand(101, 999);
        list($month, $year) = explode('-', $facture->getMois());
        $tel = '084';
        $numbers = $tel.$id.$year.$month;

        return substr($numbers, 0, 3).'/'.substr($numbers, 3, 4).'/'.substr($numbers, 7, 5);
    }


}
