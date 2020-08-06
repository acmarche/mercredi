<?php

namespace AcMarche\Mercredi\Presence\Dto;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Jour;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Pour le listing par mois
 * Class JourListing.
 */
final class JourListing
{
    /**
     * @var ArrayCollection
     */
    protected $enfants;
    /**
     * @var Jour
     */
    private $jour;

    public function __construct(Jour $jour, array $enfants)
    {
        $this->enfants = new ArrayCollection($enfants);
        $this->jour = $jour;
    }

    public function hasEnfant(Enfant $enfant): bool
    {
        return $this->enfants->contains($enfant);
    }

    public function getEnfants(): ArrayCollection
    {
        return $this->enfants;
    }

    public function setEnfants(ArrayCollection $arrayCollection): void
    {
        $this->enfants = $arrayCollection;
    }

    public function getJour(): Jour
    {
        return $this->jour;
    }

    public function setJour(Jour $jour): void
    {
        $this->jour = $jour;
    }
}
