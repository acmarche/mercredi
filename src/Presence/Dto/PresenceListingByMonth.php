<?php


namespace AcMarche\Mercredi\Presence\Dto;


use Doctrine\Common\Collections\ArrayCollection;

class PresenceListingByMonth
{
    protected $enfants;

    /**
     * @var JourListing[]
     */
    protected $days;

    /**
     * pour chaque jour je dois avoir les enfants
     *
     *
     *
     * pour chaque enfant je dois avec ses présences
     *
     *
     */

    public function __construct(array $enfants)
    {
        $this->days = new ArrayCollection();
        $this->enfants = $enfants;
    }




}
