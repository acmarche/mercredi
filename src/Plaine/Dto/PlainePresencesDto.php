<?php

namespace AcMarche\Mercredi\Plaine\Dto;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class PlainePresencesDto
{
    /**
     * @var Jour[]|ArrayCollection
     */
    public $daysOfPlaine;
    private $plaine;
    private $enfant;
    private $jours;

    public function __construct(Plaine $plaine, Enfant $enfant, iterable $daysOfPlaine)
    {
        $this->plaine = $plaine;
        $this->enfant = $enfant;
        $this->jours = new ArrayCollection();
        $this->daysOfPlaine = $daysOfPlaine;
    }

    /**
     * @return mixed
     */
    public function getEnfant()
    {
        return $this->enfant;
    }

    public function getPlaine(): Plaine
    {
        return $this->plaine;
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
