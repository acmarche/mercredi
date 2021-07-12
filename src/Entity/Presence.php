<?php

namespace AcMarche\Mercredi\Entity;

use AcMarche\Mercredi\Entity\Security\Traits\UserAddTrait;
use AcMarche\Mercredi\Entity\Traits\AbsentTrait;
use AcMarche\Mercredi\Entity\Traits\EnfantTrait;
use AcMarche\Mercredi\Entity\Traits\HalfTrait;
use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\JourTrait;
use AcMarche\Mercredi\Entity\Traits\OrdreTrait;
use AcMarche\Mercredi\Entity\Traits\ReductionTrait;
use AcMarche\Mercredi\Entity\Traits\RemarqueTrait;
use AcMarche\Mercredi\Entity\Traits\TuteurTrait;
use AcMarche\Mercredi\Presence\Entity\PresenceInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\UuidableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Knp\DoctrineBehaviors\Model\Uuidable\UuidableTrait;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use AcMarche\Mercredi\Entity\Facture\FacturePresence;

/**
 * @ORM\Table("presence", uniqueConstraints={
 *     @ORM\UniqueConstraint(columns={"jour_id", "enfant_id"})
 * })
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Presence\Repository\PresenceRepository")
 * @UniqueEntity(fields={"jour", "enfant"}, message="L'enfant est déjà inscrit à cette date")
 */
class Presence implements TimestampableInterface, PresenceInterface, UuidableInterface
{
    use IdTrait;
    use UuidableTrait;
    use EnfantTrait;
    use TuteurTrait;
    use JourTrait;
    use AbsentTrait;
    use OrdreTrait;
    use ReductionTrait;
    use RemarqueTrait;
    use UserAddTrait;
    use TimestampableTrait;
    use HalfTrait;

    /**
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Entity\Jour", inversedBy="presences")
     */
    private ?Jour $jour = null;

    /**
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Entity\Enfant", inversedBy="presences")
     */
    private ?Enfant $enfant = null;

    /**
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Entity\Tuteur", inversedBy="presences")
     */
    private ?Tuteur $tuteur = null;

    /**
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Entity\Reduction")
     */
    private ?Reduction $reduction = null;

    /**
     * @ORM\Column(type="smallint", length=2, nullable=false, options={"comment" = "-1 sans certif, 1 avec certfi"})
     */
    private int $absent;

    /**
     * @ORM\OneToMany(targetEntity=FacturePresence::class, mappedBy="presence")
     */
    private ?iterable $facture_presences;

    public function __construct(Tuteur $tuteur, Enfant $enfant, Jour $jour)
    {
        $this->tuteur = $tuteur;
        $this->enfant = $enfant;
        $this->jour = $jour;
        $this->absent = 0;
        $this->half = 0;
        $this->facture_presences = new ArrayCollection();
    }

    public function __toString()
    {
        return 'presence to string';
    }

    /**
     * @return Collection|FacturePresence[]
     */
    public function getFacturePresences(): Collection
    {
        return $this->facture_presences;
    }

    public function addFacturePresence(FacturePresence $facturePresence): self
    {
        if (!$this->facture_presences->contains($facturePresence)) {
            $this->facture_presences[] = $facturePresence;
            $facturePresence->setPresence($this);
        }

        return $this;
    }

    public function removeFacturePresence(FacturePresence $facturePresence): self
    {
        if ($this->facture_presences->removeElement($facturePresence)) {
            // set the owning side to null (unless already changed)
            if ($facturePresence->getPresence() === $this) {
                $facturePresence->setPresence(null);
            }
        }

        return $this;
    }
}
