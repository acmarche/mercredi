<?php

namespace AcMarche\Mercredi\Entity\Facture;

use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\RemarqueTrait;
use AcMarche\Mercredi\Facture\FactureInterface;
use AcMarche\Mercredi\Facture\Repository\FactureDecompteRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\UuidableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Knp\DoctrineBehaviors\Model\Uuidable\UuidableTrait;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FactureDecompteRepository::class)]
#[ORM\Table(name: 'facture_decompte')]
class FactureDecompte implements TimestampableInterface, UuidableInterface
{
    use IdTrait;
    use RemarqueTrait;
    use FactureTrait;
    use UuidableTrait;
    use TimestampableTrait;

    #[ORM\ManyToOne(targetEntity: Facture::class, inversedBy: 'factureDecomptes')]
    private FactureInterface $facture;
    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTimeInterface $payeLe = null;
    #[ORM\Column(type: 'decimal', precision: 6, scale: 2, nullable: false)]
    #[Assert\Range(min: '0.1')]
    private ?float $montant = null;

    public function __construct(
        Facture $facture
    ) {
        $this->facture = $facture;
    }

    public function getPayeLe(): ?\DateTimeInterface
    {
        return $this->payeLe;
    }

    public function setPayeLe(?\DateTimeInterface $payeLe): self
    {
        $this->payeLe = $payeLe;

        return $this;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): self
    {
        $this->montant = $montant;

        return $this;
    }
}
