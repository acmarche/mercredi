<?php

namespace AcMarche\Mercredi\Entity\Facture;

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
    private $tuteur;

    /**
     * @var \DateTime|null
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $factureLe;

    /**
     * @var \DateTime|null
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $payeLe;

    /**
     * @var Ecole[] $ecoles
     */
    private $ecoles;

    /**
     * @var Enfant[] $enfants
     */
    private $enfants;

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
}
