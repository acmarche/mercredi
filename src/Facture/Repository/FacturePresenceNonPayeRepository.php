<?php

namespace AcMarche\Mercredi\Facture\Repository;

use AcMarche\Mercredi\Accueil\Repository\AccueilRepository;
use AcMarche\Mercredi\Entity\Presence\Accueil;
use AcMarche\Mercredi\Entity\Presence\Presence;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Facture\FactureInterface;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use DateTimeInterface;

class FacturePresenceNonPayeRepository
{
    public function __construct(
        private PresenceRepository $presenceRepository,
        private AccueilRepository $accueilRepository,
        private FacturePresenceRepository $facturePresenceRepository
    ) {
    }

    /**
     * @return array|Presence[]
     */
    public function findPresencesNonPayes(Tuteur $tuteur, ?DateTimeInterface $date = null): array
    {
        $presences = $this->presenceRepository->findByTuteurAndMonth($tuteur, $date);
        $ids = array_map(
            fn ($presence) => $presence->getId(),
            $presences
        );
        $presencesPayes = $this->facturePresenceRepository->findByIdsAndType($ids, FactureInterface::OBJECT_PRESENCE);
        $idPayes = array_map(
            fn ($presence) => $presence->getPresenceId(),
            $presencesPayes
        );
        $idsNonPayes = array_diff($ids, $idPayes);

        return $this->presenceRepository->findBy([
            'id' => $idsNonPayes,
        ]);
    }

    /**
     * @return array|Accueil[]
     */
    public function findAccueilsNonPayes(Tuteur $tuteur, ?DateTimeInterface $date = null): array
    {
        $accueils = $this->accueilRepository->findByTuteurAndMonth($tuteur, $date);
        $ids = array_map(
            fn ($accueil) => $accueil->getId(),
            $accueils
        );
        $presencesPayes = $this->facturePresenceRepository->findByIdsAndType($ids, FactureInterface::OBJECT_ACCUEIL);
        $idPayes = array_map(
            fn ($presence) => $presence->getPresenceId(),
            $presencesPayes
        );
        $idsNonPayes = array_diff($ids, $idPayes);

        return $this->accueilRepository->findBy([
            'id' => $idsNonPayes,
        ]);
    }
}
