<?php

namespace AcMarche\Mercredi\Plaine\Handler;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Entity\Presence\Presence;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Plaine\Utils\PlaineUtils;
use AcMarche\Mercredi\Presence\Handler\PresenceHandler;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use Doctrine\Common\Collections\Collection;

final class PlainePresenceHandler
{
    private PresenceRepository $presenceRepository;
    private PresenceHandler $presenceHandler;

    public function __construct(PresenceRepository $presenceRepository, PresenceHandler $presenceHandler)
    {
        $this->presenceRepository = $presenceRepository;
        $this->presenceHandler = $presenceHandler;
    }

    public function handleAddEnfant(Plaine $plaine, Tuteur $tuteur, Enfant $enfant): void
    {
        $jours = PlaineUtils::extractJoursFromPlaine($plaine);
        $this->presenceHandler->handleNew($tuteur, $enfant, $jours);
    }

    public function handleEditPresences(
        Tuteur $tuteur,
        Enfant $enfant,
        array $currentJours,
        Collection $collection
    ): void {
        $enMoins = array_diff($currentJours, $collection->toArray());
        $enPlus = array_diff($collection->toArray(), $currentJours);

        foreach ($enPlus as $jour) {
            $presence = new Presence($tuteur, $enfant, $jour);
            $this->presenceRepository->persist($presence);
        }

        foreach ($enMoins as $jour) {
            $presence = $this->presenceRepository->findOneByEnfantJour($enfant, $jour);
            if (null !== $presence) {
                $this->presenceRepository->remove($presence);
            }
        }

        $this->presenceRepository->flush();
    }

    public function removeEnfant(Plaine $plaine, Enfant $enfant): void
    {
        $presences = $this->presenceRepository->findByPlaineAndEnfant($plaine, $enfant);
        foreach ($presences as $presence) {
            $this->presenceRepository->remove($presence);
        }
        $this->presenceRepository->flush();
    }
}
