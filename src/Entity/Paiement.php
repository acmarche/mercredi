<?php

namespace AcMarche\Mercredi\Entity;

use AcMarche\Mercredi\Entity\Presence\Presence;
use AcMarche\Mercredi\Entity\Security\Traits\UserAddTrait;
use AcMarche\Mercredi\Entity\Security\User;
use DateTimeImmutable;
use DateTimeInterface;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @deprecated  for migration
 * @ORM\Table("paiement")
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Migration\PaiementRepository")
 */
class Paiement
{
    use TimestampableTrait;
    use UserAddTrait;

    /**
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected ?int $id = null;

    /**
     * @ORM\Column(type="decimal", precision=6, scale=2, nullable=false)
     * @Assert\NotBlank()
     */
    protected $montant;

    /**
     * @ORM\Column(type="date", nullable=false)
     */
    protected ?DateTimeInterface $date_paiement = null;

    /**
     * @ORM\Column(type="string", nullable=true, length=150)
     *
     * @var string?
     */
    protected ?string $type_paiement = null;

    /**
     * @ORM\Column(type="string", nullable=true, length=150)
     *
     * @var string?
     */
    protected ?string $mode_paiement = null;

    /**
     * @ORM\Column(type="smallint", length=2, nullable=true, options={"comment" = "1,2, suviant", "default" = "0"})
     */
    protected ?int $ordre = 0;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default" = "0"})
     */
    protected $cloture = false;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected ?string $remarques = null;

    /**
     * @ORM\ManyToOne(targetEntity="Tuteur", inversedBy="paiements")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    protected ?Tuteur $tuteur = null;

    /**
     * @ORM\ManyToOne(targetEntity="Enfant", inversedBy="paiements")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    protected ?Enfant $enfant = null;

    /**
     * @ORM\OneToMany(targetEntity="AcMarche\Mercredi\Entity\Presence\Presence", mappedBy="paiement")
     */
    protected Collection $presences;

    /**
     * ORM\OneToMany(targetEntity="AcMarche\Mercredi\Plaine\Entity\PlainePresence", mappedBy="paiement")
     */
    protected Collection $plaine_presences;

    public function __construct()
    {
        $this->presences = new ArrayCollection();
        $this->plaine_presences = new ArrayCollection();
    }

    public function __toString()
    {
        $string = $this->getTypePaiement().' du ';
        if ($this->getDatePaiement() !== null) {
            $string .= $this->getDatePaiement()->format('d-m-Y');
        }

        return $string . (' ('.$this->getMontant().' €)');
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getMontant(): ?string
    {
        return $this->montant;
    }

    public function setMontant(string $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    /**
     * @return DateTime|DateTimeImmutable
     */
    public function getDatePaiement(): \DateTime
    {
        return $this->date_paiement;
    }

    public function setDatePaiement(DateTimeInterface $date_paiement): self
    {
        $this->date_paiement = $date_paiement;

        return $this;
    }

    public function getTypePaiement(): string
    {
        return $this->type_paiement;
    }

    public function setTypePaiement(?string $type_paiement): self
    {
        $this->type_paiement = $type_paiement;

        return $this;
    }

    public function getModePaiement(): string
    {
        return $this->mode_paiement;
    }

    public function setModePaiement(?string $mode_paiement): self
    {
        $this->mode_paiement = $mode_paiement;

        return $this;
    }

    public function getOrdre(): int
    {
        return $this->ordre;
    }

    public function setOrdre(?int $ordre): self
    {
        $this->ordre = $ordre;

        return $this;
    }

    public function getCloture(): bool
    {
        return $this->cloture;
    }

    public function setCloture(bool $cloture): self
    {
        $this->cloture = $cloture;

        return $this;
    }

    public function getRemarques(): string
    {
        return $this->remarques;
    }

    public function setRemarques(?string $remarques): self
    {
        $this->remarques = $remarques;

        return $this;
    }

    public function getTuteur(): ?Tuteur
    {
        return $this->tuteur;
    }

    public function setTuteur(?Tuteur $tuteur): self
    {
        $this->tuteur = $tuteur;

        return $this;
    }

    public function getEnfant(): ?Enfant
    {
        return $this->enfant;
    }

    public function setEnfant(?Enfant $enfant): self
    {
        $this->enfant = $enfant;

        return $this;
    }

    /**
     * @return Collection|Presence[]
     */
    public function getPresences(): Collection
    {
        return $this->presences;
    }

    public function addPresence(Presence $presence): self
    {
        if (!$this->presences->contains($presence)) {
            $this->presences[] = $presence;
            $presence->setPaiement($this);
        }

        return $this;
    }

    public function removePresence(Presence $presence): self
    {
        if ($this->presences->contains($presence)) {
            $this->presences->removeElement($presence);
            // set the owning side to null (unless already changed)
            if ($presence->getPaiement() === $this) {
                $presence->setPaiement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PlainePresence[]
     */
    public function getPlainePresences(): Collection
    {
        return $this->plaine_presences;
    }

    public function addPlainePresence(PlainePresence $plainePresence): self
    {
        if (!$this->plaine_presences->contains($plainePresence)) {
            $this->plaine_presences[] = $plainePresence;
            $plainePresence->setPaiement($this);
        }

        return $this;
    }

    public function removePlainePresence(PlainePresence $plainePresence): self
    {
        if ($this->plaine_presences->contains($plainePresence)) {
            $this->plaine_presences->removeElement($plainePresence);
            // set the owning side to null (unless already changed)
            if ($plainePresence->getPaiement() === $this) {
                $plainePresence->setPaiement(null);
            }
        }

        return $this;
    }

}
