<?php


namespace AcMarche\Mercredi\Presence\Calculator;

use AcMarche\Mercredi\Presence\Entity\PresenceInterface;

interface PresenceCalculatorInterface
{
    public function calculate(PresenceInterface $presence): float;
}
