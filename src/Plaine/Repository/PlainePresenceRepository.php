<?php


namespace AcMarche\Mercredi\Plaine\Repository;


use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Entity\Presence;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Presence\Utils\PresenceUtils;

class PlainePresenceRepository
{
    /**
     * @var PresenceRepository
     */
    private $presenceRepository;

    public function __construct(PresenceRepository $presenceRepository)
    {
        $this->presenceRepository = $presenceRepository;
    }

    /**
     * @param Plaine $plaine
     * @return Enfant[]
     */
    public function findEnfantsByPlaine(Plaine $plaine): array
    {
        $presences = $this->presenceRepository->findPresencesByPlaine($plaine);

        return PresenceUtils::extractEnfants($presences);
    }

    /**
     * @param Plaine $plaine
     * @param Enfant $enfant
     * @return Presence[]
     */
    public function findPrecencesByPlaineAndEnfant(Plaine $plaine, Enfant $enfant)
    {
        return $this->presenceRepository->findPresencesByPlaineAndEnfant($plaine, $enfant);
    }
}
