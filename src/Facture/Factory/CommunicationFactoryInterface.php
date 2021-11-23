<?php

namespace AcMarche\Mercredi\Facture\Factory;

use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Facture\FactureInterface;

interface CommunicationFactoryInterface
{
    public function generateForPresence(FactureInterface $facture): string;

    public function generateForPlaine(Plaine $plaine, FactureInterface $facture): string;
}
