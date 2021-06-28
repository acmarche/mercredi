<?php

namespace AcMarche\Mercredi\Facture\Utils;

use AcMarche\Mercredi\Accueil\Repository\AccueilRepository;
use AcMarche\Mercredi\Entity\Accueil;
use AcMarche\Mercredi\Entity\Presence;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Facture\Repository\FactureAccueilRepository;
use AcMarche\Mercredi\Facture\Repository\FacturePresenceRepository;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;

final class FactureUtils
{
    private PresenceRepository $presenceRepository;
    private FacturePresenceRepository $facturePresenceRepository;
    private FactureAccueilRepository $factureAccueilRepository;
    private AccueilRepository $accueilRepository;

    public function __construct(
        PresenceRepository $presenceRepository,
        AccueilRepository $accueilRepository,
        FacturePresenceRepository $facturePresenceRepository,
        FactureAccueilRepository $factureAccueilRepository
    ) {
        $this->presenceRepository = $presenceRepository;
        $this->facturePresenceRepository = $facturePresenceRepository;
        $this->factureAccueilRepository = $factureAccueilRepository;
        $this->accueilRepository = $accueilRepository;
    }

    /**
     * @return Presence[]
     */
    public function getPresencesNonPayees(Tuteur $tuteur): array
    {
        $presencesAll = $this->presenceRepository->findPresencesByTuteur($tuteur);
        $presencesNonFacturees = [];
        foreach ($presencesAll as $presence) {
            if (null === $this->facturePresenceRepository->findByPresence($presence)) {
                $presencesNonFacturees[] = $presence;
            }
        }

        return $presencesNonFacturees;
    }

    /**
     * @return Accueil[]
     */
    public function getAccueilsNonPayes(Tuteur $tuteur): array
    {
        $all = $this->accueilRepository->findByTuteur($tuteur);
        $nonFacturees = [];
        foreach ($all as $accueil) {
            if (null === $this->factureAccueilRepository->findByAccueil($accueil)) {
                $nonFacturees[] = $accueil;
            }
        }

        return $nonFacturees;
    }
}
