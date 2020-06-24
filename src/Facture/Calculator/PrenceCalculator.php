<?php


namespace AcMarche\Mercredi\Facture\Calculator;


use AcMarche\Mercredi\Presence\Entity\PresenceInterface;

class PrenceCalculator implements PresenceCalculatorInterface
{

    public function calculate(PresenceInterface $presence):float {

        return 2.1;
    }
}
