<?php


namespace AcMarche\Mercredi\Relation\Utils;


use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Presence;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;

class OrdreService
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
     * Ordre de l'enfant par importance decroissante.
     *
     * @param Enfant $enfant
     * @param Tuteur $tuteur
     * @param Presence $presence
     * @return int
     */
    public function getOrdreEnfant(Enfant $enfant, Tuteur $tuteur): int
    {
        $relation = $this->relationRepository->findOneByTuteurAndEnfant($tuteur, $enfant);
        if ($relation) {
            if ($ordre = $relation->getOrdre()) {
                return $ordre;
            }
        }

        return $enfant->getOrdre();
    }

}
