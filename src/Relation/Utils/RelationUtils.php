<?php

namespace AcMarche\Mercredi\Relation\Utils;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Relation;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;

class RelationUtils
{
    /**
     * @var RelationRepository
     */
    private $relationRepository;

    public function __construct(RelationRepository $relationRepository)
    {
        $this->relationRepository = $relationRepository;
    }

    /**
     * @param Tuteur $tuteur
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
