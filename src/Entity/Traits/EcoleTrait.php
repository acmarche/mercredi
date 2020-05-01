<?php


namespace AcMarche\Mercredi\Entity\Traits;

use AcMarche\Mercredi\Entity\Ecole;

trait EcoleTrait
{
    /**
     * @var Ecole|null
     */
    private $ecole;

    /**
     * @return Ecole|null
     */
    public function getEcole(): ?Ecole
    {
        return $this->ecole;
    }

    /**
     * @param Ecole|null $ecole
     */
    public function setEcole(?Ecole $ecole): void
    {
        $this->ecole = $ecole;
    }
}
