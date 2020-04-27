<?php


namespace AcMarche\Mercredi\Entity\Traits;


use AcMarche\Mercredi\Entity\Reduction;

trait ReductionTrait
{
    /**
     * @var Reduction|null
     */
    protected $reduction;

    /**
     * @return Reduction|null
     */
    public function getReduction(): ?Reduction
    {
        return $this->reduction;
    }

    /**
     * @param Reduction|null $reduction
     */
    public function setReduction(?Reduction $reduction): void
    {
        $this->reduction = $reduction;
    }

}
