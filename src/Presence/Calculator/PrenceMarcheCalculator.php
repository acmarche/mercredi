<?php

namespace AcMarche\Mercredi\Presence\Calculator;

use AcMarche\Mercredi\Presence\Entity\PresenceInterface;

final class PrenceMarcheCalculator implements PresenceCalculatorInterface
{
    public function calculate(PresenceInterface $presence): float
    {
        return 2.1;
    }
}
