<?php

namespace AcMarche\Mercredi\Entity;

use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\NomTrait;
use AcMarche\Mercredi\Entity\Traits\RemarqueTrait;
use AcMarche\Mercredi\Reduction\Validator as AcMarcheReductionAssert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Reduction\Repository\ReductionRepository")
 * @AcMarcheReductionAssert\PourcentageOrForfait()
 */
class Reduction
{
    use IdTrait;
    use NomTrait;
    use RemarqueTrait;

    /**
     *
     * @ORM\Column(type="float", nullable=true)
     * @Assert\Range(
     *      min = 0,
     *      max = 100
     *     )
     */
    private ?float $pourcentage = null;

    /**
     *
     * @ORM\Column(type="float", nullable=true)
     * @Assert\Range(
     *      min = 0
     *     )
     */
    private ?float $forfait = null;

    public function __toString()
    {
        return $this->getNom() . ' (' . $this->pourcentage . '%)';
    }

    public function getPourcentage(): ?float
    {
        return $this->pourcentage;
    }

    public function setPourcentage(?float $pourcentage): self
    {
        $this->pourcentage = $pourcentage;

        return $this;
    }

    public function getForfait(): ?float
    {
        return $this->forfait;
    }

    public function setForfait(?float $forfait): self
    {
        $this->forfait = $forfait;

        return $this;
    }
}
