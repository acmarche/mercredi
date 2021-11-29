<?php


namespace AcMarche\Mercredi\Facture\Repository;

use AcMarche\Mercredi\Accueil\Repository\AccueilRepository;
use AcMarche\Mercredi\Entity\Presence\Accueil;
use AcMarche\Mercredi\Entity\Presence\Presence;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Facture\FactureInterface;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;

class FacturePresenceNonPayeRepository
{
    private PresenceRepository $presenceRepository;
    private FacturePresenceRepository $facturePresenceRepository;
    private AccueilRepository $accueilRepository;

    public function __construct(
        PresenceRepository $presenceRepository,
        AccueilRepository $accueilRepository,
        FacturePresenceRepository $facturePresenceRepository
    ) {
        $this->presenceRepository = $presenceRepository;
        $this->facturePresenceRepository = $facturePresenceRepository;
        $this->accueilRepository = $accueilRepository;
    }

    /**
     * @param \AcMarche\Mercredi\Entity\Tuteur $tuteur
     * @return array|Presence[]
     */
    public function findPresencesNonPayes(Tuteur $tuteur, ?\DateTimeInterface $date = null): array
    {
        $presences = $this->presenceRepository->findByTuteurAndMonth($tuteur, $date);
        $ids = array_map(
            function ($presence) {
                return $presence->getId();
            },
            $presences
        );
        $presencesPayes = $this->facturePresenceRepository->findByIdsAndType($ids, FactureInterface::OBJECT_PRESENCE);
        $idPayes = array_map(
            function ($presence) {
                return $presence->getPresenceId();
            },
            $presencesPayes
        );
        $idsNonPayes = array_diff($ids, $idPayes);

        return $this->presenceRepository->findBy(['id' => $idsNonPayes]);
    }

    /**
     * @param \AcMarche\Mercredi\Entity\Tuteur $tuteur
     * @return array|Accueil[]
     */
    public function findAccueilsNonPayes(Tuteur $tuteur, ?\DateTimeInterface $date = null): array
    {
        $accueils = $this->accueilRepository->findByTuteurAndMonth($tuteur, $date);
        $ids = array_map(
            function ($accueil) {
                return $accueil->getId();
            },
            $accueils
        );
        $presencesPayes = $this->facturePresenceRepository->findByIdsAndType($ids, FactureInterface::OBJECT_ACCUEIL);
        $idPayes = array_map(
            function ($presence) {
                return $presence->getPresenceId();
            },
            $presencesPayes
        );
        $idsNonPayes = array_diff($ids, $idPayes);

        return $this->accueilRepository->findBy(['id' => $idsNonPayes]);
    }
}
