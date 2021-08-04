<?php

namespace AcMarche\Mercredi\Relation\Utils;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Presence\Entity\PresenceInterface;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;
use function count;

final class OrdreService
{
    public $ordreService;
    private RelationRepository $relationRepository;
    private PresenceRepository $presenceRepository;

    public function __construct(RelationRepository $relationRepository, PresenceRepository $presenceRepository)
    {
        $this->relationRepository = $relationRepository;
        $this->presenceRepository = $presenceRepository;
    }

    /**
     * Ordre de l'enfant par importance decroissante.
     */
    public function getOrdreEnfant(Enfant $enfant, Tuteur $tuteur): int
    {
        $relation = $this->relationRepository->findOneByTuteurAndEnfant($tuteur, $enfant);
        if (null !== $relation && ($ordre = $relation->getOrdre())) {
            return $ordre;
        }

        return $enfant->getOrdre();
    }

    public function getOrdreOnPresence(PresenceInterface $presence): float
    {
        //on a forcé l'ordre
        if (0 !== ($ordre = $presence->getOrdre())) {
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
            if (null !== $this->presenceRepository->findByEnfantAndJour($fratry, $jour)) {
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
