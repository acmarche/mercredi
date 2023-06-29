<?php

namespace AcMarche\Mercredi\Presence\Dto;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Presence\Presence;
use AcMarche\Mercredi\Jour\Repository\JourRepository;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use DateTimeInterface;

final class ListingPresenceByMonth
{
    /**
     * @var Presence[]
     */
    public array $presences;
    /**
     * @var Enfant[]
     */
    public array $enfants;
    /**
     * @var JourListing[]
     */
    public array $joursListing;

    public function __construct(
        private PresenceRepository $presenceRepository,
        private JourRepository $jourRepository
    ) {
    }

    public function create(DateTimeInterface $dateTime, ?bool $filter = null): self
    {
        $daysOfMonth = $this->jourRepository->findDaysByMonth($dateTime, $filter);

        $this->presences = $this->presenceRepository->findByDays($daysOfMonth);
        $this->enfants = $this->getEnfantsPresentsOfMonth();

        $joursListing = [];

        foreach ($daysOfMonth as $jour) {
            $presences = $this->presenceRepository->findByDay($jour);
            $enfantsByday = array_map(
                fn($presence) => $presence->getEnfant(),
                $presences
            );
            $joursListing[] = new JourListing($jour, $enfantsByday);
        }
        $this->joursListing = $joursListing;

        return $this;
    }

    /**
     * @return Enfant[]
     */
    private function getEnfantsPresentsOfMonth(): array
    {
        $enfants = array_map(
            fn($presence) => $presence->getEnfant(),
            $this->presences
        );

        return array_unique($enfants);
    }
}
