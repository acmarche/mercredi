<?php

namespace AcMarche\Mercredi\Presence\Dto;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Traits\EnfantTrait;

final class PresenceSelectDays
{
    use EnfantTrait;

    protected array $jours;

    public function __construct(Enfant $enfant)
    {
        $this->jours = [];
        $this->enfant = $enfant;
    }

    public function getJours(): array
    {
        return $this->jours;
    }

    public function setJours(array $jours): void
    {
        $this->jours = $jours;
    }
}
