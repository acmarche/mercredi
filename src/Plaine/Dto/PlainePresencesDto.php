<?php

namespace AcMarche\Mercredi\Plaine\Dto;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Entity\Presence;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class PlainePresencesDto
{
    private $plaine;

    private $enfant;

    private $jours;

    private $presences;

    public function __construct(Plaine $plaine, Enfant $enfant)
    {
        $this->plaine = $plaine;
        $this->presences = new ArrayCollection();
        $this->jours = new ArrayCollection();
        $this->enfant = $enfant;
    }

    /**
     * @return mixed
     */
    public function getEnfant()
    {
        return $this->enfant;
    }

    /**
     * @param mixed $enfant
     */
    public function setEnfant($enfant): void
    {
        $this->enfant = $enfant;
    }

    public function getPlaine(): Plaine
    {
        return $this->plaine;
    }

    public function setPlaine(Plaine $plaine): void
    {
        $this->plaine = $plaine;
    }

    /**
     * @param mixed $jours
     */
    public function setJours($jours): void
    {
        foreach ($jours as $jour) {
            $this->addJour($jour);
        }
    }

    /**
     * @param mixed $presences
     */
    public function setPresences($presences): void
    {
        $this->presences = $presences;
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
        if (!$this->presences->contains($presence)) {
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

    /**
     * @return Collection|Jour[]
     */
    public function getJours(): Collection
    {
        return $this->jours;
    }

    public function addJour(Jour $jour): self
    {
        if (!$this->jours->contains($jour)) {
            $this->jours[] = $jour;
        }

        return $this;
    }

    public function removeJour(Jour $jour): self
    {
        if ($this->jours->contains($jour)) {
            $this->jours->removeElement($jour);
        }

        return $this;
    }
}
