<?php

namespace AcMarche\Mercredi\Entity\Facture;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

trait FactureDecomptesTrait
{
    /**
     * @var FactureDecompte[]|ArrayCollection
     * @ORM\OneToMany(targetEntity=FactureDecompte::class, mappedBy="facture", cascade={"remove"})
     */
    private iterable $factureDecomptes;

    /**
     * @return Collection|FactureDecompte[]
     */
    public function getFactureDecomptes(): Collection
    {
        return $this->factureDecomptes;
    }

    public function addFactureDecompte(FactureDecompte $factureDecompte): self
    {
        if (! $this->factureDecomptes->contains($factureDecompte)) {
            $this->factureDecomptes[] = $factureDecompte;
            $factureDecompte->setFacture($this);
        }

        return $this;
    }

    public function removeFactureDecompte(FactureDecompte $factureDecompte): self
    {
        if ($this->factureDecomptes->contains($factureDecompte)) {
            $this->factureDecomptes->removeElement($factureDecompte);
            // set the owning side to null (unless already changed)
            if ($factureDecompte->getFacture() === $this) {
                $factureDecompte->setFacture(null);
            }
        }

        return $this;
    }
}
