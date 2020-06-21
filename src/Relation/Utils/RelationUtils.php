<?php

namespace AcMarche\Mercredi\Relation\Utils;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Relation;
use AcMarche\Mercredi\Entity\Tuteur;

class RelationUtils
{
    /**
     * @param Relation[] $relations
     *
     * @return Tuteur[]
     */
    public static function extractTuteurs(array $relations): array
    {
        return array_unique(
            array_map(
                function ($relation) {
                    return $relation->getTuteur();
                },
                $relations
            )
        );
    }

    /**
     * @param Relation[] $relations
     *
     * @return Enfant[]
     */
    public static function extractEnfants(array $relations): array
    {
        return array_unique(
            array_map(
                function ($relation) {
                    return $relation->getEnfant();
                },
                $relations
            )
        );
    }
}
