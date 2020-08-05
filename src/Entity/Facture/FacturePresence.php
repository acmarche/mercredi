<?php

namespace AcMarche\Mercredi\Entity\Facture;

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
 * @ORM\UniqueConstraint(columns={"presence_id"})
 * })
 * @UniqueEntity(fields={"presence"}, message="Présence déjà payée")
 */
class FacturePresence
{
    use IdTrait;
    use NomTrait;
    use PrenomTrait;
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
     */
    private $presence;

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
