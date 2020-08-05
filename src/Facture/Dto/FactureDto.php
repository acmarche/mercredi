<?php

namespace AcMarche\Mercredi\Facture\Dto;

use AcMarche\Mercredi\Entity\Accueil;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Entity\Presence;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class FactureDto
{
    /**
     * @var Presence[]
     */
    private $presences;

    /**
     * @var Accueil[]
     */
    private $accueils;

    /**
     * @var Plaine[]
     */
    private $plaines;

    public function __construct(array $presences)
    {
        $this->presences = new ArrayCollection($presences);
    }

    /**
     * @return Collection|Presence[]
     */
    public function getPresences(): Collection
    {
        return $this->presences;
    }

    public function addPresence(Presence $presence): self
    {
        if (! $this->presences->contains($presence)) {
            $this->presences[] = $presence;
        }

        return $this;
    }

    public function removePresence(Presence $presence): self
    {
        if ($this->presences->contains($presence)) {
            $this->presences->removeElement($presence);
        }

        return $this;
    }
}
