<?php

namespace AcMarche\Mercredi\Entity\Facture;

use AcMarche\Mercredi\Entity\Accueil;
use AcMarche\Mercredi\Entity\Traits\AccueilTrait;
use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\NomTrait;
use AcMarche\Mercredi\Entity\Traits\PrenomTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Facture\Repository\FactureAccueilRepository")
 * @ORM\Table("facture_accueil", uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"accueil_id"})
 * })
 * @UniqueEntity(fields={"accueil"}, message="Accueil déjà payé")
 */
class FactureAccueil
{
    use IdTrait;
    use NomTrait;
    use PrenomTrait;
    use FactureTrait;
    use AccueilTrait;

    /**
     * @var Facture
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Entity\Facture\Facture", inversedBy="factureAccueils")
     */
    private $facture;

    /**
     * @var Accueil
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Entity\Accueil")
     */
    private $accueil;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $accueilDate;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $heure;

    /**
     * @var float
     * @ORM\Column(type="decimal", precision=4, scale=2, nullable=false)
     */
    private $cout;

    public function __construct(Facture $facture, Accueil $accueil)
    {
        $this->facture = $facture;
        $this->accueil = $accueil;
    }

    public function getAccueilDate(): ?\DateTimeInterface
    {
        return $this->accueilDate;
    }

    public function setAccueilDate(\DateTimeInterface $accueilDate): self
    {
        $this->accueilDate = $accueilDate;

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

    public function getHeure(): ?string
    {
        return $this->heure;
    }

    public function setHeure(string $heure): self
    {
        $this->heure = $heure;

        return $this;
    }
}
