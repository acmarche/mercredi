<?php

namespace AcMarche\Mercredi\Entity\Plaine;

use AcMarche\Mercredi\Entity\Jour;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

trait JoursTrait
{
    /**
     * @var Jour[]|ArrayCollection
     *
     * */
    private iterable $jours;

    public function initJours(): void
    {
        $this->jours = new ArrayCollection();
    }

    /**
     * @return Collection|Jour[]
     */
    public function getJours(): Collection
    {
        if (!$this->jours) {
            $this->jours = new ArrayCollection();
        }

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
