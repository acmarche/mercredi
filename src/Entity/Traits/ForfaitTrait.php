<?php

namespace AcMarche\Mercredi\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait ForfaitTrait
{
    /**
     * @var float
     * @ORM\Column(type="decimal", precision=4, scale=2, nullable=false)
     */
    private $forfait;

    public function getForfait(): float
    {
        return $this->forfait;
    }

    public function setForfait(float $forfait): void
    {
        $this->forfait = $forfait;
    }
}
