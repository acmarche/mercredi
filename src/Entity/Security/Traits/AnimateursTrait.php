<?php

namespace AcMarche\Mercredi\Entity\Security\Traits;

use AcMarche\Mercredi\Entity\Animateur;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

trait AnimateursTrait
{
    /**
     * @var Animateur[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AcMarche\Mercredi\Entity\Animateur", inversedBy="users")
     */
    private iterable $animateurs;

    /**
     * @return Animateur|null
     */
    public function getAnimateur(): ?Animateur
    {
        $animateurs = $this->animateurs;
        if (count($animateurs) > 0) {
            return $animateurs[0];
        }

        return null;
    }

    /**
     * @return Collection|Animateur[]
     */
    public function getAnimateurs(): Collection
    {
        return $this->animateurs;
    }

    public function addAnimateur(Animateur $animateur): self
    {
        if (!$this->animateurs->contains($animateur)) {
            $this->animateurs[] = $animateur;
        }

        return $this;
    }

    public function removeAnimateur(Animateur $animateur): self
    {
        if ($this->animateurs->contains($animateur)) {
            $this->animateurs->removeElement($animateur);
        }

        return $this;
    }
}
