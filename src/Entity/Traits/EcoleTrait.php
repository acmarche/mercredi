<?php

namespace AcMarche\Mercredi\Entity\Traits;

use AcMarche\Mercredi\Entity\Ecole;

trait EcoleTrait
{
    /**
     * @var Ecole|null
     */
    private $ecole;

    public function getEcole(): ?Ecole
    {
        return $this->ecole;
    }

    public function setEcole(?Ecole $ecole): void
    {
        $this->ecole = $ecole;
    }
}
