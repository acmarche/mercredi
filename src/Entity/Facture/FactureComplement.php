<?php

namespace AcMarche\Mercredi\Entity\Facture;

use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\NomTrait;
use AcMarche\Mercredi\Facture\Validator as AcMarcheValidator;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\UuidableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Knp\DoctrineBehaviors\Model\Uuidable\UuidableTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Facture\Repository\FactureComplementRepository")
 * @ORM\Table("facture_complement")
 * @AcMarcheValidator\PourcentageOrForfait()
 */
class FactureComplement implements TimestampableInterface, UuidableInterface
{
    use IdTrait;
    use NomTrait;
    use FactureTrait;
    use UuidableTrait;
    use TimestampableTrait;

    /**
     * @ORM\ManyToOne(targetEntity=Facture::class, inversedBy="factureComplements")
     */
    private Facture $facture;
    /**
     * @ORM\Column(type="decimal", precision=4, scale=2, nullable=true)
     * @Assert\Range(
     *      min = 0
     * )
     */
    private ?float $forfait = null;
    /**
     * @ORM\Column(type="decimal", precision=4, scale=2, nullable=true)
     * @Assert\Range(
     *      min = 0,
     *      max = 100
     *)
     */
    private ?float $pourcentage = null;

    public function __construct(Facture $facture)
    {
        $this->facture = $facture;
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

    public function getPourcentage(): ?float
    {
        return $this->pourcentage;
    }

    public function setPourcentage(?float $pourcentage): self
    {
        $this->pourcentage = $pourcentage;

        return $this;
    }
}
