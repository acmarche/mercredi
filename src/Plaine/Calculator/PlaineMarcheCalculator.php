<?php

namespace AcMarche\Mercredi\Plaine\Calculator;

use AcMarche\Mercredi\Contrat\Plaine\PlaineCalculatorInterface;
use AcMarche\Mercredi\Contrat\Presence\PresenceInterface;
use AcMarche\Mercredi\Entity\Plaine\Plaine;

final class PlaineMarcheCalculator implements PlaineCalculatorInterface
{
    public function calculate(Plaine $plaine, array $presences): float
    {
        
        return 0;
    }

    public function calculateOnePresence(Plaine $plaine, PresenceInterface $presence): float
    {
        
        return 0;
    }

    public function getOrdreOnePresence(PresenceInterface $presence): int
    {
        
    }

    public function getPrixByOrdre(Plaine $plaine, $ordre): float
    {
        
    }
}
