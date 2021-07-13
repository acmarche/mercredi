<?php

namespace AcMarche\Mercredi\Entity;

use DateTimeInterface;
use AcMarche\Mercredi\Entity\Security\Traits\UserAddTrait;
use AcMarche\Mercredi\Entity\Traits\EnfantTrait;
use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\RemarqueTrait;
use AcMarche\Mercredi\Entity\Traits\TuteurTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\UuidableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Knp\DoctrineBehaviors\Model\Uuidable\UuidableTrait;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use AcMarche\Mercredi\Entity\Facture\FactureAccueil;

/**
 * Class Accueil.
 *
 * @ORM\Table("accueil")
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Accueil\Repository\AccueilRepository")
 * @UniqueEntity(fields={"date_jour", "enfant", "heure"}, message="L'enfant est déjà inscrit à cette date")
 */
class Accueil implements TimestampableInterface, UuidableInterface
{
    use TimestampableTrait;
    use IdTrait;
    use UuidableTrait;
    use EnfantTrait;
    use TuteurTrait;
    use RemarqueTrait;
    use UserAddTrait;

    /**
     * @ORM\Column(name="date_jour", type="date")
     * @Assert\Type("datetime")
     */
    private ?DateTimeInterface $date_jour = null;

    /**
     * @ORM\Column(type="smallint")
     */
    private int $duree;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private ?string $heure = null;

    /**
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Entity\Enfant", inversedBy="accueils")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Enfant $enfant = null;

    /**
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Entity\Tuteur", inversedBy="accueils")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Tuteur $tuteur = null;

    /**
     * @ORM\OneToMany(targetEntity=FactureAccueil::class, mappedBy="accueil")
     */
    private ?iterable $facture_accueils;

    public function __construct(Tuteur $tuteur, Enfant $enfant)
    {
        $this->enfant = $enfant;
        $this->tuteur = $tuteur;
        $this->duree = 0;
        $this->facture_accueils = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->date_jour->format('Y-m-d');
    }

    public function getDateJour(): ?\DateTimeInterface
    {
        return $this->date_jour;
    }

    public function setDateJour(\DateTimeInterface $date_jour): self
    {
        $this->date_jour = $date_jour;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): self
    {
        $this->duree = $duree;

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

    /**
     * @return Collection|FactureAccueil[]
     */
    public function getFactureAccueils(): Collection
    {
        return $this->facture_accueils;
    }

    public function addFactureAccueil(FactureAccueil $factureAccueil): self
    {
        if (!$this->facture_accueils->contains($factureAccueil)) {
            $this->facture_accueils[] = $factureAccueil;
            $factureAccueil->setAccueil($this);
        }

        return $this;
    }

    public function removeFactureAccueil(FactureAccueil $factureAccueil): self
    {
        if ($this->facture_accueils->removeElement($factureAccueil)) {
            // set the owning side to null (unless already changed)
            if ($factureAccueil->getAccueil() === $this) {
                $factureAccueil->setAccueil(null);
            }
        }

        return $this;
    }
}
