<?php

namespace AcMarche\Mercredi\Presence\Calculator;

use AcMarche\Mercredi\Entity\Facture\FacturePresence;
use AcMarche\Mercredi\Presence\Entity\PresenceInterface;

interface PresenceCalculatorInterface
{
    public function setMetaDatas(PresenceInterface $presence, FacturePresence $facturePresence): void;

    public function calculate(PresenceInterface $presence): float;

    public function getOrdreOnPresence(PresenceInterface $presence): int;

    public function getPrixByOrdre(PresenceInterface $presence, int $ordre): float;
}
