<?php

namespace AcMarche\Mercredi\Relation\Utils;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Relation;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;

final class RelationUtils
{
    private RelationRepository $relationRepository;

    public function __construct(RelationRepository $relationRepository)
    {
        $this->relationRepository = $relationRepository;
    }

    /**
     * @return Enfant[]
     */
    public function findEnfantsByTuteur(Tuteur $tuteur): array
    {
        $relations = $this->relationRepository->findByTuteur($tuteur);

        return self::extractEnfants($relations);
    }

    /**
     * @param Relation[] $relations
     *
     * @return Tuteur[]
     */
    public static function extractTuteurs(array $relations): array
    {
        return array_unique(
            array_map(
                fn($relation) => $relation->getTuteur(),
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
                fn($relation) => $relation->getEnfant(),
                $relations
            )
        );
    }
}
