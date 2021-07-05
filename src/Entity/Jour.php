<?php

namespace AcMarche\Mercredi\Entity;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use AcMarche\Mercredi\Entity\Plaine\PlaineJour;
use AcMarche\Mercredi\Entity\Plaine\PlaineJourTrait;
use AcMarche\Mercredi\Entity\Traits\AnimateursTrait;
use AcMarche\Mercredi\Entity\Traits\ArchiveTrait;
use AcMarche\Mercredi\Entity\Traits\ColorTrait;
use AcMarche\Mercredi\Entity\Traits\ForfaitTrait;
use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\PedagogiqueTrait;
use AcMarche\Mercredi\Entity\Traits\PrixTrait;
use AcMarche\Mercredi\Entity\Traits\RemarqueTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Jour\Repository\JourRepository")
 * @UniqueEntity("date_jour")
 */
class Jour implements TimestampableInterface
{
    use IdTrait;
    use TimestampableTrait;
    use PrixTrait;
    use ColorTrait;
    use RemarqueTrait;
    use ArchiveTrait;
    use PedagogiqueTrait;
    use ForfaitTrait;
    use PlaineJourTrait;
    use AnimateursTrait;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="date_jour", type="date", unique=true)
     * @Assert\Type("datetime")
     */
    private ?DateTimeInterface $date_jour;

    /**
     * J'ai mis la definition pour pouvoir mettre le cascade.
     *
     * @var Presence[]
     * @ORM\OneToMany(targetEntity="AcMarche\Mercredi\Entity\Presence", mappedBy="jour", cascade={"remove"})
     */
    private iterable $presences;

    /**
     * @ORM\OneToOne(targetEntity="AcMarche\Mercredi\Entity\Plaine\PlaineJour", mappedBy="jour")
     */
    private ?PlaineJour $plaine_jour = null;

    /**
     * @var Animateur[]
     * @ORM\ManyToMany(targetEntity="AcMarche\Mercredi\Entity\Animateur", mappedBy="jours")
     */
    private iterable $animateurs;

    /**
     * @ORM\ManyToMany(targetEntity="AcMarche\Mercredi\Entity\Ecole")
     */
    private iterable $ecoles;

    /**
     * @param DateTime|DateTimeImmutable|null $date_jour
     */
    public function __construct(?DateTimeInterface $date_jour = null)
    {
        $this->prix1 = 0;
        $this->prix2 = 0;
        $this->prix3 = 0;
        $this->forfait = 0;
        $this->pedagogique = false;
        $this->presences = new ArrayCollection();
        $this->animateurs = new ArrayCollection();
        $this->ecoles = new ArrayCollection();
        $this->date_jour = $date_jour;
    }

    public function __toString()
    {
        return $this->date_jour->format('d-m-Y');
    }

    public function getDateJour(): \DateTime
    {
        return $this->date_jour;
    }

    /**
     * @param DateTime|DateTimeImmutable|null $date_jour
     */
    public function setDateJour(?DateTimeInterface $date_jour): void
    {
        $this->date_jour = $date_jour;
    }

    /**
     * @return Collection|Presence[]
     */
    public function getPresences(): array
    {
        return $this->presences;
    }

    public function addPresence(Presence $presence): self
    {
        if (! $this->presences->contains($presence)) {
            $this->presences[] = $presence;
            $presence->setJour($this);
        }

        return $this;
    }

    public function removePresence(Presence $presence): self
    {
        if ($this->presences->contains($presence)) {
            $this->presences->removeElement($presence);
            // set the owning side to null (unless already changed)
            if ($presence->getJour() === $this) {
                $presence->setJour(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Ecole[]
     */
    public function getEcoles(): iterable
    {
        return $this->ecoles;
    }

    public function addEcole(Ecole $ecole): self
    {
        if (!$this->ecoles->contains($ecole)) {
            $this->ecoles[] = $ecole;
        }

        return $this;
    }

    public function removeEcole(Ecole $ecole): self
    {
        if ($this->ecoles->contains($ecole)) {
            $this->ecoles->removeElement($ecole);
        }

        return $this;
    }
}
