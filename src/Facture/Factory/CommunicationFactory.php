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
        $id2 = rand(101, 999);
        list($month, $year) = explode('-', $facture->getMois());
        $numbers = $id.$id2.$year.$month;

        $communication = substr($numbers, 0, 3).'/'.substr($numbers, 3, 4).'/'.substr($numbers, 7, 5);
        if ($this->factureRepository->findOneBy(['communication' => $communication])) {
        return    $this->generate($facture);
        }

        return $communication;
    }


}
