<?php


namespace AcMarche\Mercredi\Plaine\Utils;


use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Entity\Plaine\Plaine;

class PlaineUtils
{ /**
     * @param Plaine $plaine
     * @return Jour[]
     */
    public static function extractJoursFromPlaine(Plaine $plaine)
    {
        $plaineJours = $plaine->getPlaineJours();
        $jours = [];
        foreach ($plaineJours as $plaineJour) {
            $jours[] = $plaineJour->getJour();
        }
        return $jours;
    }
}
