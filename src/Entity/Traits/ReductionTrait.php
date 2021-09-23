<?php

namespace AcMarche\Mercredi\Entity\Traits;

use AcMarche\Mercredi\Entity\Reduction;
use Doctrine\ORM\Mapping as ORM;

trait ReductionTrait
{
    /**
     * @ORM\ManyToOne(targetEntity=Reduction::class)
     */
    private ?Reduction $reduction = null;

    public function getReduction(): ?Reduction
    {
        return $this->reduction;
    }

    public function setReduction(?Reduction $reduction): void
    {
        $this->reduction = $reduction;
    }
}
