<?php


namespace AcMarche\Mercredi\Entity\Plaine;


use Doctrine\Common\Collections\Collection;

trait PlaineMaxTrait
{

    /**
     * @return Collection|PlaineGroupe[]
     */
    public function getPlaineGroupes(): Collection
    {
        return $this->max;
    }

    public function addMax(PlaineGroupe $max): self
    {
        if (!$this->max->contains($max)) {
            $this->max[] = $max;
            $max->setPlaine($this);
        }

        return $this;
    }

    public function removeMax(PlaineGroupe $max): self
    {
        if ($this->max->contains($max)) {
            $this->max->removeElement($max);
            // set the owning side to null (unless already changed)
            if ($max->getPlaine() === $this) {
                $max->setPlaine(null);
            }
        }

        return $this;
    }
}
