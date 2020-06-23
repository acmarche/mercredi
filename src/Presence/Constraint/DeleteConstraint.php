<?php

namespace AcMarche\Mercredi\Presence\Constraint;

use AcMarche\Mercredi\Entity\Presence;
use DateTime;

class DeleteConstraint
{
    public static function canBeDeleted(Presence $presence)
    {
        $today = new DateTime();
        if ($presence->getJour()->getDateJour() <= $today) {
            return false;
        }

        return true;
    }
}
