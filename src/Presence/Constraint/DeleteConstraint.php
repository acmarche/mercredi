<?php

namespace AcMarche\Mercredi\Presence\Constraint;

use AcMarche\Mercredi\Entity\Accueil;
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

    public static function accueilCanBeDeleted(Accueil $presence)
    {
        $today = new DateTime();
        if ($presence->getDateJour() <= $today) {
            return false;
        }

        return true;
    }

    public function constraintDelete(\DateTimeInterface $datePresence, $today = null): bool
    {
        /*
         * Si on est un mardi la veille !
         * alors il faut qu'on soit max mardi 12h00
         * si on reserve un mardi 6 pour un admin 7
         */
        if (2 === $today->format('N')) {
            $lendemain = clone $today;
            $lendemain = $lendemain->modify('+1 day');
            //la veille ?
            if ($lendemain->format('d-m-Y') === $datePresence->format('d-m-Y')) {
                //si après 10h
                $heure = (int) $today->format('G');
                $minute = (int) $today->format('i');
                if ($heure > 10) {
                    return false;
                }
                if (10 === $heure) {
                    //si après 10h02
                    if ($minute > 02) {
                        return false;
                    }
                }
            }
        }

        return true;
    }
}
