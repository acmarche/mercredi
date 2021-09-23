<?php

namespace AcMarche\Mercredi\Entity\Facture;

use AcMarche\Mercredi\Entity\Security\Traits\UserAddTrait;
use AcMarche\Mercredi\Entity\Traits\AdresseTrait;
use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\NomTrait;
use AcMarche\Mercredi\Entity\Traits\PrenomTrait;
use AcMarche\Mercredi\Entity\Traits\ReductionTrait;
use AcMarche\Mercredi\Entity\Traits\RemarqueTrait;
use AcMarche\Mercredi\Entity\Traits\TuteurTrait;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Facture\FactureInterface;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\UuidableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Knp\DoctrineBehaviors\Model\Uuidable\UuidableTrait;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Facture\Repository\FactureRepository")
 *
 */
class Facture implements TimestampableInterface, UuidableInterface, FactureInterface
{
    use IdTrait;
    use NomTrait;
    use PrenomTrait;
    use TuteurTrait;
    use AdresseTrait;
    use TimestampableTrait;
    use RemarqueTrait;
    use UuidableTrait;
    use FacturePresencesTrait;
    use FactureReductionsTrait;
    use FactureComplementsTrait;
    use UserAddTrait;

    /**
     * @ORM\ManyToOne(targetEntity=Tuteur::class, inversedBy="factures")
     */
    private ?Tuteur $tuteur = null;
    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private ?DateTimeInterface $factureLe = null;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTimeInterface $payeLe = null;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTimeInterface $envoyeLe = null;
    /**
     * @ORM\Column(type="string", length=100,nullable=true)
     */
    private ?string $envoyeA = null;
    /**
     * @ORM\Column(type="string", length=100,nullable=false)
     */
    private ?string $mois = null;
    /**
     * @ORM\Column(type="string", length=100,nullable=false, unique=true)
     */
    private ?string $communication = null;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $plaine = null;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $ecoles = null;

    /**
     * @ORM\Column(type="decimal", precision=6, scale=2, nullable=true)
     */
    private float $montant_obsolete;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private float $cloture_obsolete;

    public function __construct(Tuteur $tuteur)
    {
        $this->tuteur = $tuteur;
        $this->facturePresences = new ArrayCollection();
        $this->factureReductions = new ArrayCollection();
        $this->factureComplements = new ArrayCollection();
    }

    public function __toString()
    {
        return 'Facture '.$this->id;
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

    public function getFactureLe(): ?\DateTimeInterface
    {
        return $this->factureLe;
    }

    public function setFactureLe(\DateTimeInterface $factureLe): self
    {
        $this->factureLe = $factureLe;

        return $this;
    }

    public function getEnvoyeLe(): ?\DateTimeInterface
    {
        return $this->envoyeLe;
    }

    public function setEnvoyeLe(?\DateTimeInterface $envoyeLe): self
    {
        $this->envoyeLe = $envoyeLe;

        return $this;
    }

    public function getEnvoyeA(): ?string
    {
        return $this->envoyeA;
    }

    public function setEnvoyeA(?string $envoyeA): self
    {
        $this->envoyeA = $envoyeA;

        return $this;
    }

    public function getMois(): ?string
    {
        return $this->mois;
    }

    public function setMois(string $mois): self
    {
        $this->mois = $mois;

        return $this;
    }

    public function getCommunication(): ?string
    {
        return $this->communication;
    }

    public function setCommunication(string $communication): self
    {
        $this->communication = $communication;

        return $this;
    }

    public function getPlaine(): ?string
    {
        return $this->plaine;
    }

    public function setPlaine(?string $plaine): self
    {
        $this->plaine = $plaine;

        return $this;
    }

    public function getEcoles(): ?string
    {
        return $this->ecoles;
    }

    public function setEcoles(?string $ecoles): self
    {
        $this->ecoles = $ecoles;

        return $this;
    }

    public function getMontantObsolete(): ?string
    {
        return $this->montant_obsolete;
    }

    public function setMontantObsolete(?string $montant_obsolete): self
    {
        $this->montant_obsolete = $montant_obsolete;

        return $this;
    }

    public function getClotureObsolete(): ?bool
    {
        return $this->cloture_obsolete;
    }

    public function setClotureObsolete(?bool $cloture_obsolete): self
    {
        $this->cloture_obsolete = $cloture_obsolete;

        return $this;
    }
}
