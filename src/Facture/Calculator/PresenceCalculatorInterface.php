<?php


namespace AcMarche\Mercredi\Facture\Calculator;

use AcMarche\Mercredi\Presence\Entity\PresenceInterface;

interface PresenceCalculatorInterface
{
    public function calculate(PresenceInterface $presence): float;
}
