<?php

namespace AcMarche\Mercredi\Entity\Traits;

use AcMarche\Mercredi\Entity\Jour;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

trait JoursTrait
{
    /**
     * @var Jour[]|ArrayCollection
     *
     * */
    private $jours;

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
            $jour->setPlaine($this);
        }

        return $this;
    }

    public function removeJour(Jour $jour): self
    {
        if ($this->jours->contains($jour)) {
            $this->jours->removeElement($jour);
            // set the owning side to null (unless already changed)
            if ($jour->getPlaine() === $this) {
                $jour->setPlaine(null);
            }
        }

        return $this;
    }

}
