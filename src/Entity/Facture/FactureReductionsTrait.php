<?php

namespace AcMarche\Mercredi\Entity\Facture;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

trait FactureReductionsTrait
{
    /**
     * @var FactureReduction[]|ArrayCollection
     * @ORM\OneToMany(targetEntity=FactureReduction::class, mappedBy="facture", cascade={"remove"})
     */
    private iterable $factureReductions;

    /**
     * @return Collection|FactureReduction[]
     */
    public function getFactureReductions(): Collection
    {
        return $this->factureReductions;
    }

    public function addFactureReduction(FactureReduction $factureReduction): self
    {
        if (! $this->factureReductions->contains($factureReduction)) {
            $this->factureReductions[] = $factureReduction;
            $factureReduction->setFacture($this);
        }

        return $this;
    }

    public function removeFactureReduction(FactureReduction $factureReduction): self
    {
        if ($this->factureReductions->contains($factureReduction)) {
            $this->factureReductions->removeElement($factureReduction);
            // set the owning side to null (unless already changed)
            if ($factureReduction->getFacture() === $this) {
                $factureReduction->setFacture(null);
            }
        }

        return $this;
    }
}
