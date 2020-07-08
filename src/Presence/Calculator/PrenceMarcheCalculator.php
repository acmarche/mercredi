<?php

namespace AcMarche\Mercredi\Presence\Calculator;

use AcMarche\Mercredi\Entity\Presence;
use AcMarche\Mercredi\Presence\Entity\PresenceInterface;

class PrenceMarcheCalculator implements PresenceCalculatorInterface
{
    public function calculate(PresenceInterface $presence): float
    {
        $presence = new Presence();

        return 2.1;
    }
}
