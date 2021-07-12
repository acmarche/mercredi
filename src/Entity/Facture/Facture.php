<?php

namespace AcMarche\Mercredi\Entity\Facture;

use DateTimeInterface;
use AcMarche\Mercredi\Entity\Ecole;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Security\Traits\UserAddTrait;
use AcMarche\Mercredi\Entity\Traits\AdresseTrait;
use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\NomTrait;
use AcMarche\Mercredi\Entity\Traits\PrenomTrait;
use AcMarche\Mercredi\Entity\Traits\RemarqueTrait;
use AcMarche\Mercredi\Entity\Traits\TuteurTrait;
use AcMarche\Mercredi\Entity\Tuteur;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\UuidableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Knp\DoctrineBehaviors\Model\Uuidable\UuidableTrait;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Facture\Repository\FactureRepository")
 */
class Facture implements TimestampableInterface, UuidableInterface
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
    use FactureAccueilsTrait;
    use UserAddTrait;

    /**
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Entity\Tuteur", inversedBy="factures")
     */
    private ?Tuteur $tuteur;
    /**
     * @ORM\Column(type="datetime", nullable=true)
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
     * @ORM\Column(type="string", length=100,nullable=false)
     */
    private ?string $communication = null;

    /**
     * @var Ecole[] $ecoles
     */
    private array $ecoles;

    /**
     * @var Enfant[] $enfants
     */
    private array $enfants;

    public function __construct(Tuteur $tuteur)
    {
        $this->tuteur = $tuteur;
        $this->facturePresences = new ArrayCollection();
        $this->factureAccueils = new ArrayCollection();
    }

    public function __toString()
    {
        return 'Facture '.$this->id;
    }

    /**
     * @return array|Enfant[]
     */
    public function getEnfants(): array
    {
        $enfants = [];
        foreach ($this->facturePresences as $facturePresence) {
            $presence = $facturePresence->getPresence();
            $enfant = $presence->getEnfant();
            $enfants[$enfant->getId()] = $enfant;
        }
        foreach ($this->getFactureAccueils() as $factureAccueil) {
            $accueil = $factureAccueil->getAccueil();
            $enfant = $accueil->getEnfant();
            $enfants[$enfant->getId()] = $enfant;
        }

        return $enfants;
    }

    /**
     * @return array|Ecole[]
     */
    public function getEcoles(): array
    {
        $ecoles = [];
        foreach ($this->getEnfants() as $enfant) {
            $ecole = $enfant->getEcole();
            $ecoles[$ecole->getId()] = $ecole;
        }

        return $ecoles;
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

    public function setFactureLe(?\DateTimeInterface $factureLe): self
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
}
