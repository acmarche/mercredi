<?php


namespace AcMarche\Mercredi\Presence\Dto;


use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Jour;
use Doctrine\Common\Collections\ArrayCollection;

class JourListing
{
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

    public function hasEnfant(Enfant $enfant): bool {
        return $this->enfants->contains($enfant);
    }

    /**
     * @return ArrayCollection
     */
    public function getEnfants(): ArrayCollection
    {
        return $this->enfants;
    }

    /**
     * @param ArrayCollection $enfants
     */
    public function setEnfants(ArrayCollection $enfants): void
    {
        $this->enfants = $enfants;
    }

    /**
     * @return Jour
     */
    public function getJour(): Jour
    {
        return $this->jour;
    }

    /**
     * @param Jour $jour
     */
    public function setJour(Jour $jour): void
    {
        $this->jour = $jour;
    }



}
