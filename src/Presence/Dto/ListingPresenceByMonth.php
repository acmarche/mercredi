<?php

namespace AcMarche\Mercredi\Presence\Dto;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Entity\Presence;
use AcMarche\Mercredi\Jour\Repository\JourRepository;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;

class ListingPresenceByMonth
{
    /**
     * @var Presence[]
     */
    protected $presences;
    /**
     * @var Enfant[]
     */
    protected $enfants;
    /**
     * @var JourListing[]
     */
    protected $joursListing;
    /**
     * @var PresenceRepository
     */
    private $presenceRepository;
    /**
     * @var JourRepository
     */
    private $jourRepository;

    public function __construct(PresenceRepository $presenceRepository, JourRepository $jourRepository)
    {
        $this->presenceRepository = $presenceRepository;
        $this->jourRepository = $jourRepository;
    }

    public function create(\DateTimeInterface $month): self
    {
        $daysOfMonth = $this->getDaysOfMonth($month);
        $this->presences = $this->getPresencesOfMonth($month);
        $this->enfants = $this->getEnfantsPresentsOfMonth($month);

        $joursListing = [];

        foreach ($daysOfMonth as $jour) {
            $presences = $this->presenceRepository->findByDay($jour);
            $enfantsByday = array_map(
                function ($presence) {
                    return $presence->getEnfant();
                },
                $presences
            );
            $joursListing[] = new JourListing($jour, $enfantsByday);
        }
        $this->joursListing = $joursListing;

        return $this;
    }

    /**
     * @return Jour[]
     */
    public function getDaysOfMonth(\DateTimeInterface $month)
    {
        return $this->jourRepository->findDaysByMonth($month);
    }

    /**
     * @return Presence[]
     */
    public function getPresences(): array
    {
        return $this->presences;
    }

    /**
     * @return Enfant[]
     */
    public function getEnfants(): array
    {
        return $this->enfants;
    }

    /**
     * @return JourListing[]
     */
    public function getJoursListing(): array
    {
        return $this->joursListing;
    }

    /**
     * @return Presence[]
     */
    private function getPresencesOfMonth(\DateTimeInterface $month): array
    {
        return $this->presenceRepository->findByMonth($month);
    }

    /**
     * @return Enfant[]
     */
    private function getEnfantsPresentsOfMonth(\DateTimeInterface $month): array
    {
        $enfants = array_map(
            function ($presence) {
                return $presence->getEnfant();
            },
            $this->presences
        );

        $enfants = array_unique($enfants);

        return $enfants;
    }
}
