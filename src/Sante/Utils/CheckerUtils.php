<?php


namespace AcMarche\Mercredi\Sante\Utils;


use AcMarche\Mercredi\Entity\Enfant;

class CheckerUtils
{

    public function ficheIsComplete(Enfant $enfant)
    {
        if (!$enfant->getNom()) {
            return false;
        }

        if (!$enfant->getPrenom()) {
            return false;
        }

        if (!$enfant->getEcole()) {
            return false;
        }

        if (!$enfant->getAnneeScolaire()) {
            return false;
        }

        return true;
    }

    /**
     * @param Enfant[] $enfants
     */
    public function checkFicheEnfants($enfants)
    {
        foreach ($enfants as $enfant) {
            $enfant->setFicheComplete(self::ficheIsComplete($enfant));
        }
    }
}
