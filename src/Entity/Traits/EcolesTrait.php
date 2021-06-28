<?php

namespace AcMarche\Mercredi\Entity\Traits;

use AcMarche\Mercredi\Entity\Ecole;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

trait EcolesTrait
{
    /**
     * @var Ecole[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AcMarche\Mercredi\Entity\Ecole", inversedBy="users")
     */
    private iterable $ecoles;

    /**
     * @return Collection|Ecole[]
     */
    public function getEcoles(): Collection
    {
        return $this->ecoles;
    }

    public function addEcole(Ecole $ecole): self
    {
        if (! $this->ecoles->contains($ecole)) {
            $this->ecoles[] = $ecole;
        }

        return $this;
    }

    public function removeEcole(Ecole $ecole): self
    {
        if ($this->ecoles->contains($ecole)) {
            $this->ecoles->removeElement($ecole);
        }

        return $this;
    }
}
