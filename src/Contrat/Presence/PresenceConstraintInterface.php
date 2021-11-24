<?php

namespace AcMarche\Mercredi\Contrat\Presence;

use AcMarche\Mercredi\Entity\Jour;

interface PresenceConstraintInterface
{
    public function addFlashError(Jour $jour);

    public function check(Jour $jour): bool;
}
