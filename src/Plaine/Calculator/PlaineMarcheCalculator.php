<?php

namespace AcMarche\Mercredi\Plaine\Calculator;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Presence\Entity\PresenceInterface;

final class PlaineMarcheCalculator implements PlaineCalculatorInterface
{
    public function calculate(Plaine $plaine, array $presences): float
    {
        // TODO: Implement calculate() method.
        return 0;
    }

    public function calculateOnePresence(Plaine $plaine, PresenceInterface $presence): float
    {
        // TODO: Implement calculate() method.
        return 0;
    }

    public function getOrdreOnePresence(PresenceInterface $presence): int
    {
        // TODO: Implement getOrdreOnePresence() method.
    }

    public function getPrixByOrdre(Plaine $plaine, $ordre): float
    {
        // TODO: Implement getPrixByOrdre() method.
    }
}
