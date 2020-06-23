<?php

namespace AcMarche\Mercredi\Entity;

use AcMarche\Mercredi\Entity\Sante\Traits\FicheSanteIsCompleteTrait;
use AcMarche\Mercredi\Entity\Sante\Traits\SanteFicheTrait;
use AcMarche\Mercredi\Entity\Traits\AgeTrait;
use AcMarche\Mercredi\Entity\Traits\ArchiveTrait;
use AcMarche\Mercredi\Entity\Traits\BirthdayTrait;
use AcMarche\Mercredi\Entity\Traits\EcoleTrait;
use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\NomTrait;
use AcMarche\Mercredi\Entity\Traits\OrdreTrait;
use AcMarche\Mercredi\Entity\Traits\PhotoTrait;
use AcMarche\Mercredi\Entity\Traits\PrenomTrait;
use AcMarche\Mercredi\Entity\Traits\RelationsTrait;
use AcMarche\Mercredi\Entity\Traits\RemarqueTrait;
use AcMarche\Mercredi\Entity\Traits\SexeTrait;
use AcMarche\Mercredi\Entity\Traits\TelephonesTrait;
use AcMarche\Mercredi\Entity\Security\Traits\UserAddTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\UuidableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Knp\DoctrineBehaviors\Model\Uuidable\UuidableTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Enfant\Repository\EnfantRepository")
 * @Vich\Uploadable
 */
class Enfant implements SluggableInterface, TimestampableInterface, UuidableInterface
{
    use IdTrait;
    use NomTrait;
    use PrenomTrait;
    use BirthdayTrait;
    use SexeTrait;
    use RemarqueTrait;
    use OrdreTrait;
    use PhotoTrait;
    use UserAddTrait;
    use SluggableTrait;
    use EcoleTrait;
    use RelationsTrait;
    use ArchiveTrait;
    use TimestampableTrait;
    use TelephonesTrait;
    use AgeTrait;
    use SanteFicheTrait;
    use FicheSanteIsCompleteTrait;
    use UuidableTrait;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $numero_national;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $photo_autorisation = false;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=200, nullable=false)
     * @Assert\NotBlank()
     */
    private $annee_scolaire;

    /**
     * @var string|null
     *                  Forcer le groupe scolaire
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $groupe_scolaire;

    /**
     * @var Ecole|null
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Entity\Ecole")
     */
    private $ecole;

    /**
     * @var Relation[]
     * @ORM\OneToMany(targetEntity="AcMarche\Mercredi\Entity\Relation", mappedBy="enfant", cascade={"remove"})
     */
    private $relations;

    /**
     * J'ai mis la definition pour pouvoir mettre le cascade.
     *
     * @var Presence[]
     * @ORM\OneToMany(targetEntity="AcMarche\Mercredi\Entity\Presence", mappedBy="enfant", cascade={"remove"})
     */
    private $presences;

    public function __construct()
    {
        $this->relations = new ArrayCollection();
        $this->presences = new ArrayCollection();
        $this->ficheSanteIsComplete = false;
    }

    public function __toString()
    {
        return mb_strtoupper($this->nom, 'UTF-8').' '.$this->prenom;
    }

    public function getSluggableFields(): array
    {
        return ['nom', 'prenom'];
    }

    public function shouldGenerateUniqueSlugs(): bool
    {
        return true;
    }

    public function getNumeroNational(): ?string
    {
        return $this->numero_national;
    }

    public function setNumeroNational(?string $numero_national): void
    {
        $this->numero_national = $numero_national;
    }

    public function isPhotoAutorisation(): bool
    {
        return $this->photo_autorisation;
    }

    public function setPhotoAutorisation(bool $photo_autorisation): void
    {
        $this->photo_autorisation = $photo_autorisation;
    }

    public function getAnneeScolaire(): ?string
    {
        return $this->annee_scolaire;
    }

    public function setAnneeScolaire(?string $annee_scolaire): void
    {
        $this->annee_scolaire = $annee_scolaire;
    }

    public function getGroupeScolaire(): ?string
    {
        return $this->groupe_scolaire;
    }

    public function setGroupeScolaire(?string $groupe_scolaire): void
    {
        $this->groupe_scolaire = $groupe_scolaire;
    }

    public function getPhotoAutorisation(): ?bool
    {
        return $this->photo_autorisation;
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
            $presence->setEnfant($this);
        }

        return $this;
    }

    public function removePresence(Presence $presence): self
    {
        if ($this->presences->contains($presence)) {
            $this->presences->removeElement($presence);
            // set the owning side to null (unless already changed)
            if ($presence->getEnfant() === $this) {
                $presence->setEnfant(null);
            }
        }

        return $this;
    }
}
