<?php

namespace AcMarche\Mercredi\Plaine\Dto;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

final class PlainePresencesDto
{
    private Collection $jours;

    public function __construct(
        private Plaine $plaine,
        private Enfant $enfant,
        public iterable $daysOfPlaine
    ) {
        $this->jours = new ArrayCollection();
    }

    public function getEnfant(): Enfant
    {
        return $this->enfant;
    }

    public function getPlaine(): Plaine
    {
        return $this->plaine;
    }

    /**
     * @param Jour[] $jours
     */
    public function setJours(array $jours): void
    {
        foreach ($jours as $jour) {
            $this->addJour($jour);
        }
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
        if (! $this->jours->contains($jour)) {
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
