<?php

namespace AcMarche\Mercredi\Entity\Facture;

use AcMarche\Mercredi\Entity\Security\Traits\UserAddTrait;
use AcMarche\Mercredi\Entity\Traits\AdresseTrait;
use AcMarche\Mercredi\Entity\Traits\IdTrait;
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
    use TuteurTrait;
    use AdresseTrait;
    use TimestampableTrait;
    use RemarqueTrait;
    use UuidableTrait;
    use FacturePresencesTrait;
    use UserAddTrait;

    /**
     * @var string
     * @ORM\Column(type="string", length=150, nullable=false)
     */
    private $tuteurNom;

    /**
     * @var string
     * @ORM\Column(type="string", length=150, nullable=false)
     */
    private $tuteurPrenom;

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

    public function __construct(Tuteur $tuteur)
    {
        $this->tuteur = $tuteur;
        $this->facturePresences = new ArrayCollection();
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

    public function getTuteurNom(): ?string
    {
        return $this->tuteurNom;
    }

    public function setTuteurNom(string $tuteurNom): self
    {
        $this->tuteurNom = $tuteurNom;

        return $this;
    }

    public function getTuteurPrenom(): ?string
    {
        return $this->tuteurPrenom;
    }

    public function setTuteurPrenom(string $tuteurPrenom): self
    {
        $this->tuteurPrenom = $tuteurPrenom;

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
