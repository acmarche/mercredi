<?php

namespace AcMarche\Mercredi\Entity\Facture;

use AcMarche\Mercredi\Entity\Traits\AbsentTrait;
use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\NomTrait;
use AcMarche\Mercredi\Entity\Traits\OrdreTrait;
use AcMarche\Mercredi\Entity\Traits\PedagogiqueTrait;
use AcMarche\Mercredi\Entity\Traits\PrenomTrait;
use AcMarche\Mercredi\Entity\Traits\ReductionTrait;
use AcMarche\Mercredi\Facture\FactureInterface;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Facture\Repository\FacturePresenceRepository")
 * @ORM\Table("facture_presence", uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"presence_id", "object_type"})
 * })
 * @UniqueEntity(fields={"presence", "objectType"}, message="Présence existante")
 */
class FacturePresence
{
    use IdTrait;
    use NomTrait;
    use PrenomTrait;
    use FactureTrait;
    use PedagogiqueTrait;
    use OrdreTrait;
    use AbsentTrait;
    use ReductionTrait;

    /**
     * @ORM\ManyToOne(targetEntity=Facture::class, inversedBy="facturePresences")
     */
    private FactureInterface $facture;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private ?int $presenceId = null;

    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    private ?string $objectType = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $heure = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $duree = null;

    /**
     * @ORM\Column(type="date", nullable=false)
     */
    private ?DateTimeInterface $presenceDate = null;

    /**
     * @ORM\Column(type="decimal", precision=4, scale=2, nullable=false)
     */
    private ?float $cout_brut = null;

    /**
     * @ORM\Column(type="decimal", precision=4, scale=2, nullable=false)
     */
    private ?float $cout_calculated = null;

    public function __construct(FactureInterface $facture, int $presenceId, string $objectType)
    {
        $this->facture = $facture;
        $this->presenceId = $presenceId;
        $this->objectType = $objectType;
    }

    public function getPresenceDate(): ?\DateTimeInterface
    {
        return $this->presenceDate;
    }

    public function setPresenceDate(\DateTimeInterface $presenceDate): self
    {
        $this->presenceDate = $presenceDate;

        return $this;
    }

    public function getCoutCalculated(): ?float
    {
        return $this->cout_calculated;
    }

    public function setCoutCalculated(float $cout_calculated): self
    {
        $this->cout_calculated = $cout_calculated;

        return $this;
    }
    public function getCoutBrut(): ?float
    {
        return $this->cout_brut;
    }

    public function setCoutBrut(float $coutBrut): self
    {
        $this->cout_brut = $coutBrut;

        return $this;
    }

    public function getPresenceId(): ?int
    {
        return $this->presenceId;
    }

    public function setPresenceId(int $presenceId): self
    {
        $this->presenceId = $presenceId;

        return $this;
    }

    public function getObjectType(): ?string
    {
        return $this->objectType;
    }

    public function setObjectType(string $objectType): self
    {
        $this->objectType = $objectType;

        return $this;
    }

    public function getHeure(): ?string
    {
        return $this->heure;
    }

    public function setHeure(string $heure): self
    {
        $this->heure = $heure;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(?int $duree): self
    {
        $this->duree = $duree;

        return $this;
    }
}
