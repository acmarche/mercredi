<?php

namespace AcMarche\Mercredi\Entity\Facture;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

trait FactureAccueilsTrait
{
    /**
     * @var FactureAccueil[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="AcMarche\Mercredi\Entity\Facture\FactureAccueil", mappedBy="facture", cascade={"remove"})
     */
    private iterable $factureAccueils;

    /**
     * @return Collection|FactureAccueil[]
     */
    public function getFactureAccueils(): Collection
    {
        return $this->factureAccueils;
    }

    public function addFactureAccueil(FactureAccueil $factureAccueil): self
    {
        if (!$this->factureAccueils->contains($factureAccueil)) {
            $this->factureAccueils[] = $factureAccueil;
            $factureAccueil->setFacture($this);
        }

        return $this;
    }

    public function removeFactureAccueil(FactureAccueil $factureAccueil): self
    {
        if ($this->factureAccueils->contains($factureAccueil)) {
            $this->factureAccueils->removeElement($factureAccueil);
            // set the owning side to null (unless already changed)
            if ($factureAccueil->getFacture() === $this) {
                $factureAccueil->setFacture(null);
            }
        }

        return $this;
    }
}
