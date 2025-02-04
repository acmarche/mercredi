<?php

namespace AcMarche\Mercredi\Entity;

use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\NomTrait;
use AcMarche\Mercredi\Entity\Traits\RemarqueTrait;
use AcMarche\Mercredi\Reduction\Repository\ReductionRepository;
use AcMarche\Mercredi\Reduction\Validator as AcMarcheReductionAssert;
use Doctrine\ORM\Mapping as ORM;
use Stringable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @AcMarcheReductionAssert\PourcentageOrForfait()
 */
#[ORM\Entity(repositoryClass: ReductionRepository::class)]
class Reduction implements Stringable
{
    use IdTrait;
    use NomTrait;
    use RemarqueTrait;

    #[ORM\Column(nullable: true)]
    #[Assert\Range(min: 0, max: 100)]
    public ?float $pourcentage = null;
    #[ORM\Column(nullable: true)]
    #[Assert\Range(min: 0)]
    public ?float $amount = null;
    #[ORM\Column(nullable: true)]
    public ?bool $is_forfait = null;

    public function __toString(): string
    {
        return $this->getNom().' ('.$this->pourcentage.'%)';
    }

    public function getIsForfait(): ?bool
    {
        return $this->is_forfait;
    }

}
