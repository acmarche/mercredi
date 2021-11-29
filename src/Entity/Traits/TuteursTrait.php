<?php

namespace AcMarche\Mercredi\Entity\Traits;

use AcMarche\Mercredi\Entity\Tuteur;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

trait TuteursTrait
{
    /**
     * @var Tuteur[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity=Tuteur::class, inversedBy="users")
     */
    private iterable $tuteurs;

    /**
     * @return Collection|Tuteur[]
     */
    public function getTuteurs(): Collection
    {
        return $this->tuteurs;
    }

    public function addTuteur(Tuteur $tuteur): self
    {
        if (!$this->tuteurs->contains($tuteur)) {
            $this->tuteurs[] = $tuteur;
        }

        return $this;
    }

    public function removeTuteur(Tuteur $tuteur): self
    {
        if ($this->tuteurs->contains($tuteur)) {
            $this->tuteurs->removeElement($tuteur);
        }

        return $this;
    }
}
