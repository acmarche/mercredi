<?php

namespace AcMarche\Mercredi\Entity;

use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Entity\Plaine\PlaineTrait;
use AcMarche\Mercredi\Entity\Presence\Presence;
use AcMarche\Mercredi\Entity\Scolaire\Ecole;
use AcMarche\Mercredi\Entity\Traits\AnimateursTrait;
use AcMarche\Mercredi\Entity\Traits\ArchiveTrait;
use AcMarche\Mercredi\Entity\Traits\ColorTrait;
use AcMarche\Mercredi\Entity\Traits\ForfaitTrait;
use AcMarche\Mercredi\Entity\Traits\IdOldTrait;
use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\PedagogiqueTrait;
use AcMarche\Mercredi\Entity\Traits\PrixTrait;
use AcMarche\Mercredi\Entity\Traits\RemarqueTrait;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @UniqueEntity("date_jour", "pedagogique", "plaine")
 */
#[ORM\Table]
#[ORM\UniqueConstraint(columns: ['date_jour', 'pedagogique', 'plaine_id'])]
#[ORM\Entity(repositoryClass: 'AcMarche\Mercredi\Jour\Repository\JourRepository')]
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
    use AnimateursTrait;
    use PlaineTrait;
    use IdOldTrait;
    /**
     * @var DateTime|null
     *
     * @Assert\Type("datetime")
     */
    #[ORM\Column(name: 'date_jour', type: 'date')]
    private ?DateTimeInterface $date_jour;
    /**
     * J'ai mis la definition pour pouvoir mettre le cascade.
     *
     * @var Presence[]
     */
    #[ORM\OneToMany(targetEntity: Presence::class, mappedBy: 'jour', cascade: ['remove'])]
    private iterable $presences;
    #[ORM\ManyToOne(targetEntity: Plaine::class, inversedBy: 'jours')]
    private ?Plaine $plaine = null;
    /**
     * @var Animateur[]
     */
    #[ORM\ManyToMany(targetEntity: Animateur::class, mappedBy: 'jours')]
    private iterable $animateurs;
    #[ORM\ManyToMany(targetEntity: Ecole::class)]
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

    public function __toString(): string
    {
        return $this->date_jour->format('d-m-Y');
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

    /**
     * @return Collection|Presence[]
     */
    public function getPresences(): iterable
    {
        return $this->presences;
    }

    public function addPresence(Presence $presence): self
    {
        if (!$this->presences->contains($presence)) {
            $this->presences[] = $presence;
            $presence->setJour($this);
        }

        return $this;
    }

    public function removePresence(Presence $presence): self
    {
        // set the owning side to null (unless already changed)
        if ($this->presences->removeElement($presence) && $presence->getJour() === $this) {
            $presence->setJour(null);
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
        $this->ecoles->removeElement($ecole);

        return $this;
    }
}
