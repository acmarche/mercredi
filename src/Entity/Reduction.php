<?php

namespace AcMarche\Mercredi\Entity;

use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\NomTrait;
use AcMarche\Mercredi\Entity\Traits\RemarqueTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Reduction\Repository\ReductionRepository")
 */
class Reduction
{
    use IdTrait;
    use NomTrait;
    use RemarqueTrait;

    /**
     * @var float|null
     *
     * @ORM\Column(type="float", nullable=false)
     *
     * @Assert\Range(
     *      min = 0,
     *      max = 100
     *     )
     */
    private $pourcentage;

    public function __toString()
    {
        return $this->getNom().' ('.$this->pourcentage.'%)';
    }

    public function getPourcentage(): ?float
    {
        return $this->pourcentage;
    }

    public function setPourcentage(?float $pourcentage): void
    {
        $this->pourcentage = $pourcentage;
    }
}
