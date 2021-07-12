<?php


namespace AcMarche\Mercredi\Facture\Factory;


use AcMarche\Mercredi\Entity\Facture\Facture;

class CommunicationFactory
{
    public static function generate(Facture $facture): string
    {
        $text = $facture->getEcoles().$facture->getMois();

        return $text;
    }
}
