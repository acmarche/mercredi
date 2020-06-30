<?php

namespace AcMarche\Mercredi\Entity;

use AcMarche\Mercredi\Entity\Plaine\PlaineTrait;
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
    use PlaineTrait;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date_jour", type="date", unique=true)
     * @Assert\Type("datetime")
     */
    private $date_jour;

    /**
     * J'ai mis la definition pour pouvoir mettre le cascade.
     *
     * @var Presence[]
     * @ORM\OneToMany(targetEntity="AcMarche\Mercredi\Entity\Presence", mappedBy="jour", cascade={"remove"})
     */
    private $presences;

    /**
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Entity\Plaine\Plaine", inversedBy="jours")
     */
    private $plaine;

    public function __construct(?\DateTime $date_jour = null)
    {
        $this->prix1 = 0;
        $this->prix2 = 0;
        $this->prix3 = 0;
        $this->forfait = 0;
        $this->pedagogique = false;
        $this->presences = new ArrayCollection();
        $this->date_jour = $date_jour;
    }

    public function __toString()
    {
        return $this->date_jour->format('d-m-Y');
    }

    public function getDateJour(): ?\DateTime
    {
        return $this->date_jour;
    }

    public function setDateJour(?\DateTime $date_jour): void
    {
        $this->date_jour = $date_jour;
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
}
