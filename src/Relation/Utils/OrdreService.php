<?php

namespace AcMarche\Mercredi\Relation\Utils;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Presence\Entity\PresenceInterface;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;

use Exception;

use function count;

final class OrdreService
{
    public $ordreService;
    /**
     * @var RelationRepository
     */
    private $relationRepository;
    /**
     * @var PresenceRepository
     */
    private $presenceRepository;

    public function __construct(RelationRepository $relationRepository, PresenceRepository $presenceRepository)
    {
        $this->relationRepository = $relationRepository;
        $this->presenceRepository = $presenceRepository;
    }

    /**
     * Ordre de l'enfant par importance decroissante.
     * @param Enfant $enfant
     * @param Tuteur $tuteur
     * @return int
     */
    public function getOrdreEnfant(Enfant $enfant, Tuteur $tuteur): int
    {
        $relation = $this->relationRepository->findOneByTuteurAndEnfant($tuteur, $enfant);
        if ($relation !== null && ($ordre = $relation->getOrdre())) {
            return $ordre;
        }

        return $enfant->getOrdre();
    }

    /**
     * @param PresenceInterface $presence
     * @return float
     */
    public function getOrdreOnPresence(PresenceInterface $presence): float
    {
        //on a forcé l'ordre
        if (($ordre = $presence->getOrdre()) !== 0) {
            return $ordre;
        }

        $tuteur = $presence->getTuteur();
        $enfant = $presence->getEnfant();
        $ordreBase = $this->getOrdreEnfant($enfant, $tuteur);
        //quand enfant premier, fraterie pas d'importance
        if (1 === $ordreBase) {
            return $ordreBase;
        }

        /**
         * Ordre suivant la fratries.
         */
        $fratries = $this->relationRepository->findFrateries(
            $enfant,
            [$tuteur]
        );

        //pas de fraterie ce jour là
        if (0 === count($fratries)) {
            return $ordreBase;
        }

        $jour = $presence->getJour();

        $presents = [];
        foreach ($fratries as $fratry) {
            if ($this->presenceRepository->findPresencesByEnfantAndJour($fratry, $jour) !== null) {
                $presents[] = $fratry;
            }
        }

        if (0 === count($presents)) {
            return $ordreBase;
        }

        //todo verifier calcul
        return $ordreBase - count($presents);
    }
}
