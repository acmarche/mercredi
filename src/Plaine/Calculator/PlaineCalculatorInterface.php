<?php

namespace AcMarche\Mercredi\Plaine\Calculator;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Presence\Entity\PresenceInterface;

interface PlaineCalculatorInterface
{
    public function calculate(Plaine $plaine, array $presences): float;
    public function calculateOnePresence(Plaine $plaine, PresenceInterface $presence): float;
}
