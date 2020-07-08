<?php

namespace AcMarche\Mercredi\Presence\Constraint;

use AcMarche\Mercredi\Entity\Presence;
use DateTime;

/**
 * Pour les parents ne peuvent supprimer une date passée
 * Class DeleteConstraint.
 */
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
