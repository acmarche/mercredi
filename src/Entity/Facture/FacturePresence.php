<?php

namespace AcMarche\Mercredi\Entity\Facture;

use AcMarche\Mercredi\Entity\Presence;
use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\PresenceTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Facture\Repository\FacturePresenceRepository")
 * @ORM\Table("facture_presence", uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"presence_id"})
 * })
 * @UniqueEntity(fields={"presence"}, message="Présence déjà payée")
 */
class FacturePresence
{
    use IdTrait;
    use FactureTrait;
    use PresenceTrait;

    /**
     * @var Facture
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Entity\Facture\Facture", inversedBy="facturePresences")
     */
    private $facture;

    /**
     * @var Presence
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Entity\Presence")
     *
     */
    private $presence;

    /**
     * @var string
     * @ORM\Column(type="string", length=150, nullable=false)
     */
    private $enfantNom;

    /**
     * @var string
     * @ORM\Column(type="string", length=150, nullable=false)
     */
    private $enfantPrenom;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $presenceDate;

    /**
     * @var float
     * @ORM\Column(type="decimal", precision=4, scale=2, nullable=false)
     */
    private $cout;

    public function __construct(Facture $facture, Presence $presence)
    {
        $this->facture = $facture;
        $this->presence = $presence;
    }

    public function getEnfantNom(): ?string
    {
        return $this->enfantNom;
    }

    public function setEnfantNom(string $enfantNom): self
    {
        $this->enfantNom = $enfantNom;

        return $this;
    }

    public function getEnfantPrenom(): ?string
    {
        return $this->enfantPrenom;
    }

    public function setEnfantPrenom(string $enfantPrenom): self
    {
        $this->enfantPrenom = $enfantPrenom;

        return $this;
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

    public function getCout(): ?float
    {
        return $this->cout;
    }

    public function setCout(float $cout): self
    {
        $this->cout = $cout;

        return $this;
    }
}
