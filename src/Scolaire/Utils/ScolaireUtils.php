<?php

namespace AcMarche\Mercredi\Scolaire\Utils;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Scolaire\GroupeScolaire;

final class ScolaireUtils
{
    /**
     * Retourne le groupe scolaire de l'enfant
     * Si a pas retourne le groupe scolaire de son année
     * Si a pas retourne un groupe au hasard.
     */
    public function findGroupeScolaireEnfantByAnneeScolaire(Enfant $enfant): GroupeScolaire
    {
        if (null !== ($groupeScolaire = $enfant->getGroupeScolaire())) {
            return $groupeScolaire;
        }

        $anneeScolaire = $enfant->getAnneeScolaire();

        if (($groupeScolaire = $anneeScolaire->getGroupeScolaire()) !== null) {
            return $groupeScolaire;
        }

        return $this->createGroupeScolaireNonClasse();
    }

    public function createGroupeScolaireNonClasse(): GroupeScolaire
    {
        $groupeScolaire = new GroupeScolaire();
        $groupeScolaire->setNom('Non classé');
        $groupeScolaire->id = 0;

        return $groupeScolaire;
    }
}
