<?php

namespace AcMarche\Mercredi\Enfant\Utils;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Tuteur;

final class EnfantUtils
{
    /**
     * @param Enfant[] $enfants
     *
     * @return Tuteur[]
     */
    public static function extractTuteurs(array $enfants): array
    {
        return array_map(
            function ($enfant) {
                return $enfant->getTuteurs();
            },
            $enfants
        );
    }
}
