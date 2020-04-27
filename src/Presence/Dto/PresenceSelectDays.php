<?php


namespace AcMarche\Mercredi\Presence\Dto;


use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Traits\EnfantTrait;

class PresenceSelectDays
{
    use EnfantTrait;

    /**
     * @var array
     */
    protected $jours;


    public function __construct(Enfant $enfant)
    {
        $this->jours = [];
        $this->enfant = $enfant;
    }

    /**
     * @return array
     */
    public function getJours(): array
    {
        return $this->jours;
    }

    /**
     * @param array $jours
     */
    public function setJours(array $jours): void
    {
        $this->jours = $jours;
    }


}
