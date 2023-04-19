<?php

namespace AcMarche\Mercredi\Relation\Utils;

use AcMarche\Mercredi\Contrat\Presence\PresenceInterface;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Presence\Presence;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;
use AcMarche\Mercredi\Utils\SortUtils;

final class OrdreService
{
    public $raison = '';

    public function __construct(
        private RelationRepository $relationRepository,
        private PresenceRepository $presenceRepository
    ) {
    }

    /**
     * Ordre de l'enfant par importance decroissante.
     */
    public function getOrdreOnRelation(Enfant $enfant, Tuteur $tuteur): ?int
    {
        $relation = $this->relationRepository->findOneByTuteurAndEnfant($tuteur, $enfant);
        if (null !== $relation && 0 !== $relation->getOrdre()) {
            return $relation->getOrdre();
        }

        return null;
    }

    public function getOrdreOnPresence(PresenceInterface $presence): int
    {
        /**
         * Ordre force sur la presence
         */
        if (0 !== ($presence->getOrdre())) {
            $this->raison = 'ordre sur la presence';

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
            $this->raison = 'ordre de base sur 1 (fiche enfant ou relation tuteur)';

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
        if ([] === $fratries) {
            $this->raison = 'pas de frateries => 1';

            return 1;
        }

        $presents = $this->getFratriesPresents($presence);

        /**
         * Pas de fratries ce jour là
         * Force premier.
         */
        $countPresents = \count($presents);
        if (0 === $countPresents) {
            $this->raison = 'tout seule ce jour-là => 1';

            return 1;
        }

        $presents[] = $enfant;
        /**
         * si pas de date naissance on force 1;
         */
        foreach ($presents as $present) {
            if (null === $present->getBirthday()) {
                $this->raison = 'pas de date de naissance => 1';

                return 1;
            }
        }

        $presents = SortUtils::sortByBirthday($presents);

        foreach ($presents as $key => $present) {
            if ($present->getId() === $enfant->getId()) {

                $this->raison = 'basé sur la fraterie ce jour là => 1';

                return $key + 1;
            }
        }

        $this->raison = 'rien trouve on force => 1';

        //force prix plein si on a pas de date naissance
        return 1;
    }

    /**
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

        if ([] === $fratries) {
            return [];
        }

        $jour = $presence->getJour();
        $presents = [];
        foreach ($fratries as $fratry) {
            if (null !== $this->presenceRepository->findByTuteurEnfantAndJourPlaineOrNot($tuteur, $fratry, $jour)) {
                $presents[] = $fratry;
            }
        }

        return $presents;
    }
}
