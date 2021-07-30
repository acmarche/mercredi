<?php

namespace AcMarche\Mercredi\Entity\Traits;

use AcMarche\Mercredi\Entity\Scolaire\Ecole;

trait EcoleTrait
{
    /**
     * @var Ecole|null
     */
    private ?Ecole $ecole=null;

    public function getEcole(): ?Ecole
    {
        return $this->ecole;
    }

    public function setEcole(?Ecole $ecole): void
    {
        $this->ecole = $ecole;
    }
}
