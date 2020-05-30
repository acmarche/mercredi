<?php


namespace AcMarche\Mercredi\Presence\Handler;


use AcMarche\Mercredi\Entity\Ecole;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Entity\Presence;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Presence\Utils\PresenceUtils;

class PresenceHandler
{
    /**
     * @var PresenceRepository
     */
    private $presenceRepository;
    /**
     * @var PresenceUtils
     */
    private $presenceUtils;

    public function __construct(PresenceRepository $presenceRepository, PresenceUtils $presenceUtils)
    {
        $this->presenceRepository = $presenceRepository;
        $this->presenceUtils = $presenceUtils;
    }

    public function handleNew(Tuteur $tuteur, Enfant $enfant, array $days)
    {
        foreach ($days as $jour) {
            if ($this->presenceRepository->exist($enfant, $jour)) {
                continue;
            }
            $presence = new Presence($tuteur, $enfant, $jour);
            $this->presenceRepository->persist($presence);
        }

        $this->presenceRepository->flush();
    }

    /**
     * @param Jour $jour
     * @param Ecole|null $ecole
     * @param bool $displayRemarque
     * @return array
     */
    public function handleForGroupe(Jour $jour, ?Ecole $ecole, bool $displayRemarque): array
    {
        $presences = $this->presenceRepository->findPresencesByJourAndEcole($jour, $ecole);

        $enfants = PresenceUtils::extractEnfants($presences, $displayRemarque);
        $this->presenceUtils->addTelephonesOnEnfant($enfants);
        $data = PresenceUtils::groupByGroupScolaire($enfants);

        return $data;
    }
}
