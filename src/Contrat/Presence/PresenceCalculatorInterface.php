<?php

namespace AcMarche\Mercredi\Contrat\Presence;

use AcMarche\Mercredi\Entity\Facture\FacturePresence;
use AcMarche\Mercredi\Contrat\Presence\PresenceInterface;

interface PresenceCalculatorInterface
{
    public function calculate(PresenceInterface $presence): float;

    public function getOrdreOnPresence(PresenceInterface $presence): int;

    public function getPrixByOrdre(PresenceInterface $presence, int $ordre): float;
}
