<?php

namespace AcMarche\Mercredi\Facture\Calculator;

use AcMarche\Mercredi\Facture\FactureInterface;

interface FactureCalculatorInterface
{
    public function coutTotal(FactureInterface $facture): float;

}
