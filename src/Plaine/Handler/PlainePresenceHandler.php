<?php

namespace AcMarche\Mercredi\Plaine\Handler;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Entity\Presence;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Jour\Repository\JourRepository;
use AcMarche\Mercredi\Plaine\Repository\PlaineRepository;
use AcMarche\Mercredi\Presence\Handler\PresenceHandler;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use Doctrine\Common\Collections\Collection;

class PlainePresenceHandler
{
    /**
     * @var PlaineRepository
     */
    private $plaineRepository;
    /**
     * @var JourRepository
     */
    private $jourRepository;
    /**
     * @var PresenceRepository
     */
    private $presenceRepository;
    /**
     * @var PresenceHandler
     */
    private $presenceHandler;

    public function __construct(
        PlaineRepository $plaineRepository,
        JourRepository $jourRepository,
        PresenceRepository $presenceRepository,
        PresenceHandler $presenceHandler
    ) {
        $this->plaineRepository = $plaineRepository;
        $this->jourRepository = $jourRepository;
        $this->presenceRepository = $presenceRepository;
        $this->presenceHandler = $presenceHandler;
    }

    public function handleAddEnfant(Plaine $plaine, Tuteur $tuteur, Enfant $enfant)
    {
        $this->presenceHandler->handleNew($tuteur, $enfant, $plaine->getJours());
    }

    public function findPresence(int $presenceId): ?Presence
    {
        return $this->presenceRepository->find($presenceId);
    }

    /**
     * @return Presence[]
     */
    public function findPresencesByPlaineEnfant(Plaine $plaine, Enfant $enfant): array
    {
        return $this->presenceRepository->findPresencesByPlaineAndEnfant($plaine, $enfant);
    }

    public function remove(Presence $presence)
    {
        $this->presenceRepository->remove($presence);
        $this->presenceRepository->flush();
    }

    public function handleEditPresence(Presence $presence)
    {
        $this->presenceRepository->flush();
    }

    public function handleEditPresences(Tuteur $tuteur, Enfant $enfant, array $currentJours, Collection $new)
    {
        $enMoins = array_diff($currentJours, $new->toArray());
        $enPlus = array_diff($new->toArray(), $currentJours);

        foreach ($enPlus as $jour) {
            $presence = new Presence($tuteur, $enfant, $jour);
            $this->presenceRepository->persist($presence);
        }

        foreach ($enMoins as $jour) {
            $presence = $this->presenceRepository->findOneByEnfantJour($enfant, $jour);
            if ($presence) {
                $this->presenceRepository->remove($presence);
            }
        }

        $this->presenceRepository->flush();
    }

    public function removeEnfant(Plaine $plaine, Enfant $enfant)
    {
        $presences = $this->findPresencesByPlaineEnfant($plaine, $enfant);
        foreach ($presences as $presence) {
            $this->presenceRepository->remove($presence);
        }
        $this->presenceRepository->flush();
    }
}