<?php

namespace AcMarche\Mercredi\Entity\Traits;

use AcMarche\Mercredi\Entity\AnneeScolaire;

trait AnneeScolaireTrait
{
    private ?AnneeScolaire $annee_scolaire = null;

    public function getAnneeScolaire(): ?AnneeScolaire
    {
        return $this->annee_scolaire;
    }

    public function setAnneeScolaire(?AnneeScolaire $groupe_scolaire): self
    {
        $this->annee_scolaire = $groupe_scolaire;

        return $this;
    }
}
