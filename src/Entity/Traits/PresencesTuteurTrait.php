<?php

namespace AcMarche\Mercredi\Entity\Traits;

use AcMarche\Mercredi\Entity\Presence;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

trait PresencesTuteurTrait
{
    /**
     * J'ai mis la definition pour pouvoir mettre le cascade.
     *
     * @var Presence[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="AcMarche\Mercredi\Entity\Presence", mappedBy="tuteur", cascade={"remove"})
     */
    private $presences;

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
            $presence->setTuteur($this);
        }

        return $this;
    }

    public function removePresence(Presence $presence): self
    {
        if ($this->presences->contains($presence)) {
            $this->presences->removeElement($presence);
            // set the owning side to null (unless already changed)
            if ($presence->getTuteur() === $this) {
                $presence->setTuteur(null);
            }
        }

        return $this;
    }
}
