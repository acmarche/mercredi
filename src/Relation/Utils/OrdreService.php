<?php

namespace AcMarche\Mercredi\Relation\Utils;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Presence\Presence;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Presence\Entity\PresenceInterface;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;
use AcMarche\Mercredi\Utils\DateUtils;
use AcMarche\Mercredi\Utils\SortUtils;
use function count;

final class OrdreService
{
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
    public function getOrdreOnRelation(Enfant $enfant, Tuteur $tuteur): ?int
    {
        $relation = $this->relationRepository->findOneByTuteurAndEnfant($tuteur, $enfant);
        if (null !== $relation && $relation->getOrdre() !== 0) {
            return $relation->getOrdre();
        }

        return null;
    }

    public function getOrdreOnPresence(PresenceInterface $presence): float
    {
        /**
         * Ordre force sur la presence
         */
        if (0 !== ($presence->getOrdre())) {
            return $presence->getOrdre();
        }

        $tuteur = $presence->getTuteur();
        $enfant = $presence->getEnfant();
        $ordreBase = $enfant->getOrdre();
        if ($ordreRelation = $this->getOrdreOnRelation($enfant, $tuteur)) {
            $ordreBase = $ordreRelation;
        }
        /**
         * quand enfant premier, fratrie pas d'importance
         */
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

        /**
         * Pas de fratries
         * Force 1
         */
        if (0 === count($fratries)) {
            return 1;
        }

        $presents = $this->getFratriesPresents($presence);

        /**
         * Pas de fratries ce jour là
         * Force premier
         */
        $countPresents = count($presents);
        if (0 === $countPresents) {
            return 1;
        }

        $presents[] = $enfant;
        /**
         * si pas de date naissance on force 1;
         */
        foreach ($presents as $present) {
            if (!$present->getBirthday()) {
                return 1;
            }
        }

        $presents = SortUtils::sortByBirthday($presents);

        foreach ($presents as $key => $present) {
                      if ($present->getId() === $enfant->getId()) {
               return $key + 1;
            }
        }

        //force prix plein si on a pas de date naissance
        return 1;
    }

    /**
     * @param \AcMarche\Mercredi\Entity\Presence\Presence $presence
     * @return array|Enfant[]
     */
    public function getFratriesPresents(Presence $presence): array
    {
        $tuteur = $presence->getTuteur();
        /**
         * Ordre suivant la fratries.
         */
        $fratries = $this->relationRepository->findFrateries(
            $presence->getEnfant(),
            [$tuteur]
        );

        if (0 === count($fratries)) {
            return [];
        }

        $jour = $presence->getJour();
        $presents = [];
        foreach ($fratries as $fratry) {
            if (null !== $this->presenceRepository->findByTuteurEnfantAndJour($tuteur, $fratry, $jour)) {
                $presents[] = $fratry;
            }
        }

        return $presents;
    }
}
