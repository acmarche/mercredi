<?php

namespace AcMarche\Mercredi\Scolaire;

use AcMarche\Mercredi\Entity\Enfant;

class ScolaireData
{
    const GROUPES_SCOLAIRES = ['premats', 'petits', 'moyens', 'grands'];
    const ANNEES_SCOLAIRES = ['PM', '1M', '2M', '3M', '1P', '2P', '3P', '4P', '5P', '6P'];

    public static function getGroupeScolaire(Enfant $enfant)
    {
        $annee_scolaire = $enfant->getAnneeScolaire();

        if (in_array($annee_scolaire, ['PM', '1M', '2M'])) {
            return 'petits';
        }

        if (in_array($annee_scolaire, ['3M', '1P', '2P'])) {
            return 'moyens';
        }

        return 'grands';
    }
}
