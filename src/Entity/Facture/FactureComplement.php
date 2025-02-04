<?php

namespace AcMarche\Mercredi\Entity\Facture;

use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\NomTrait;
use AcMarche\Mercredi\Facture\FactureInterface;
use AcMarche\Mercredi\Facture\Repository\FactureComplementRepository;
use AcMarche\Mercredi\Facture\Validator as AcMarcheValidator;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\UuidableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Knp\DoctrineBehaviors\Model\Uuidable\UuidableTrait;
use Stringable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @AcMarcheValidator\PourcentageOrForfait()
 */
#[ORM\Entity(repositoryClass: FactureComplementRepository::class)]
#[ORM\Table(name: 'facture_complement')]
class FactureComplement implements TimestampableInterface, UuidableInterface, Stringable
{
    use IdTrait;
    use NomTrait;
    use FactureTrait;
    use UuidableTrait;
    use TimestampableTrait;

    #[ORM\ManyToOne(targetEntity: Facture::class, inversedBy: 'factureComplements')]
    private FactureInterface $facture;
    #[ORM\Column(precision: 6, scale: 2, nullable: true)]
    #[Assert\Range(min: 0)]
    private ?float $amount = null;
    #[ORM\Column(precision: 6, scale: 2, nullable: true)]
    #[Assert\Range(min: 0, max: 100)]
    private ?float $pourcentage = null;
    #[ORM\Column(type: 'datetime', nullable: false)]
    private ?DateTimeInterface $dateLe = null;

    public function __construct(
        Facture $facture,
    ) {
        $this->facture = $facture;
    }

    public function __toString(): string
    {
        return "Complement facture ";
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(?float $amount): self
    {
        $this->amount = $amount;

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

    public function getDateLe(): ?\DateTimeInterface
    {
        return $this->dateLe;
    }

    public function setDateLe(\DateTimeInterface $dateLe): self
    {
        $this->dateLe = $dateLe;

        return $this;
    }
}
