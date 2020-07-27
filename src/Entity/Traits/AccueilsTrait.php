<?php


namespace AcMarche\Mercredi\Entity\Traits;


use AcMarche\Mercredi\Entity\Accueil;
use Doctrine\Common\Collections\Collection;

trait AccueilsTrait
{
    /**
     * @return Collection|Accueil[]
     */
    public function getAccueils(): Collection
    {
        return $this->accueils;
    }

    public function addAccueil(Accueil $accueil): self
    {
        if (!$this->accueils->contains($accueil)) {
            $this->accueils[] = $accueil;
            $accueil->setEnfant($this);
        }

        return $this;
    }

    public function removeAccueil(Accueil $accueil): self
    {
        if ($this->accueils->contains($accueil)) {
            $this->accueils->removeElement($accueil);
            // set the owning side to null (unless already changed)
            if ($accueil->getEnfant() === $this) {
                $accueil->setEnfant(null);
            }
        }

        return $this;
    }

}
