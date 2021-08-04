<?php

namespace AcMarche\Mercredi\Plaine\Utils;

use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Utils\SortUtils;

final class PlaineUtils
{
    /**
     * @return Jour[]
     */
    public static function extractJoursFromPlaine(Plaine $plaine): array
    {
        $plaineJours = $plaine->getPlaineJours();
        $jours = [];
        foreach ($plaineJours as $plaineJour) {
            $jours[] = $plaineJour->getJour();
        }

        return SortUtils::sortJoursByDateTime($jours);
    }
}
