<?php

namespace AcMarche\Mercredi\Entity\Facture;

use DateTimeInterface;
use DateTime;
use AcMarche\Mercredi\Entity\Presence;
use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\NomTrait;
use AcMarche\Mercredi\Entity\Traits\PrenomTrait;
use AcMarche\Mercredi\Entity\Traits\PresenceTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Facture\Repository\FacturePresenceRepository")
 * @ORM\Table("facture_presence", uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"presence_id", "object_type"})
 * })
 * @UniqueEntity(fields={"presence", "objectType"}, message="Présence déjà payée")
 */
class FacturePresence
{
    use IdTrait;
    use NomTrait;
    use PrenomTrait;
    use FactureTrait;
    use PresenceTrait;

    /**
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Entity\Facture\Facture", inversedBy="facturePresences")
     */
    private Facture $facture;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private ?int $presenceId = null;

    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    private ?string $objectType = null;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private ?string $heure = null;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private int $duree;

    /**
     * @ORM\Column(type="date", nullable=false)
     */
    private ?DateTimeInterface $presenceDate = null;

    /**
     * @ORM\Column(type="decimal", precision=4, scale=2, nullable=false)
     */
    private ?float $cout = null;

    public function __construct(Facture $facture, int $presenceId, string $objectType)
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

    public function getCout(): ?string
    {
        return $this->cout;
    }

    public function setCout(string $cout): self
    {
        $this->cout = $cout;

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

    public function getDuree(): int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): self
    {
        $this->duree = $duree;

        return $this;
    }
}
