<?php

namespace AcMarche\Mercredi\Enfant\Utils;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Tuteur;

class EnfantUtils
{
    public static function hasParents(Enfant $enfant)
    {
        $tuteurs = [];
        $enfant_tuteurs = $enfant->getTuteurs();
        foreach ($enfant_tuteurs as $enfant_tuteur) {
            $tuteur = $enfant_tuteur->getTuteur();
            $tuteurs[] = $tuteur;
        }

        return $tuteurs;
    }

    /**
     * @return Enfant[]
     */
    public function getEnfantsByTuteur(Tuteur $tuteur)
    {
        $enfant_tuteurs = $tuteur->getEnfants();
        $enfants = [];

        foreach ($enfant_tuteurs as $enfant_tuteur) {
            $enfants[] = $enfant_tuteur->getEnfant();
        }

        return $enfants;
    }

    /**
     * @param Enfant[] $enfants
     *
     * @return Tuteur[]
     */
    public static function extractTuteurs(array $enfants): array
    {
        return array_map(
            function ($enfant) {
                return $enfant->getTuteur();
            },
            $enfants
        );
    }
}
