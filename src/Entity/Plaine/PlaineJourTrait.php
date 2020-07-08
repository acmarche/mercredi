<?php

namespace AcMarche\Mercredi\Entity\Plaine;

trait PlaineJourTrait
{
    /**
     * @var PlaineJour|null
     */
    private $plaine_jour;

    public function getPlaineJour(): ?PlaineJour
    {
        return $this->plaine_jour;
    }

    public function setPlaineJour(?PlaineJour $plaine_jour): self
    {
        $this->plaine_jour = $plaine_jour;

        // set (or unset) the owning side of the relation if necessary
        $newJour = null === $plaine_jour ? null : $this;
        if ($plaine_jour->getJour() !== $newJour) {
            $plaine_jour->setJour($newJour);
        }

        return $this;
    }
}
