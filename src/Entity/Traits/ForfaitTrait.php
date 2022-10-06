<?php

namespace AcMarche\Mercredi\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait ForfaitTrait
{
    #[ORM\Column(type: 'decimal', precision: 6, scale: 2, nullable: false)]
    private float $forfait;

    public function getForfait(): float
    {
        return $this->forfait;
    }

    public function setForfait(float $forfait): void
    {
        $this->forfait = $forfait;
    }
}
