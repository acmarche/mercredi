<?php

namespace AcMarche\Mercredi\Entity\Plaine;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

trait PlaineJoursTrait
{
    /**
     * @var PlaineJour[]|ArrayCollection
     */
    private $plaine_jours;

    /**
     * @return Collection|PlaineJour[]
     */
    public function getPlaineJours(): Collection
    {
        return $this->plaine_jours;
    }

    public function addPlaineJour(PlaineJour $plaineJour): self
    {
        if (!$this->plaine_jours->contains($plaineJour)) {
            $this->plaine_jours[] = $plaineJour;
            $plaineJour->setPlaine($this);
        }

        return $this;
    }

    public function removePlaineJour(PlaineJour $plaineJour): self
    {
        if ($this->plaine_jours->contains($plaineJour)) {
            $this->plaine_jours->removeElement($plaineJour);
            // set the owning side to null (unless already changed)
            if ($plaineJour->getPlaine() === $this) {
                $plaineJour->setPlaine(null);
            }
        }

        return $this;
    }
}
