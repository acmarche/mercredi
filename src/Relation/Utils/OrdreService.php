<?php

namespace AcMarche\Mercredi\Relation\Utils;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Presence\Presence;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Presence\Entity\PresenceInterface;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;
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

        $presents = $this->getFrateriesPresents($presence);

        /**
         * Pas de fratries ce jour là
         * Force premier
         */
        $countPresents = count($presents);
        if (0 === $countPresents) {
            return 1;
        }

        $birthdayOrigine = $enfant->getBirthday();
        if ($birthdayOrigine) {
            $place = count($fratries);
            foreach ($presents as $present) {
                if ($birthday = $present->getBirthday()) {
                    if ($birthdayOrigine->format('Y-m-d') > $birthday->format('Y-m-d')) {
                        $place--;
                    } else {
                        $place++;
                    }
                }
            }
            if ($place === 0) {
                return 1;
            }
        }

        //force prix plein si on a pas de date naissance
        return 1;

        /**
         * ordre    nbre fratries    Final
         *
         * 6    1    2
         * 6    2    3
         * 6    3    4
         * 6    4    5
         * 6    5    6
         * 6    6    6
         *
         * 5    1    2
         * 5    2    3
         * 5    3    4
         * 5    4    5
         * 5    5    5
         *
         * 4    1    2
         * 4    2    3
         * 4    3    4
         * 4    4    4
         *
         * 3    1    2
         * 3    2    3
         * 3    3    3
         * 3    4    3
         *
         * 2    1    2
         * 2    2    2
         * 2    3    2
         * 2    4    2
         */
    }

    /**
     * @param \AcMarche\Mercredi\Entity\Presence\Presence $presence
     * @return array|Enfant[]
     */
    public function getFrateriesPresents(Presence $presence): array
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
