<?php

namespace AcMarche\Mercredi\Entity;

use AcMarche\Mercredi\Entity\Presence\Accueil;
use AcMarche\Mercredi\Entity\Presence\Presence;
use AcMarche\Mercredi\Entity\Sante\Traits\FicheSanteIsCompleteTrait;
use AcMarche\Mercredi\Entity\Sante\Traits\SanteFicheTrait;
use AcMarche\Mercredi\Entity\Scolaire\AnneeScolaire;
use AcMarche\Mercredi\Entity\Scolaire\Ecole;
use AcMarche\Mercredi\Entity\Scolaire\GroupeScolaire;
use AcMarche\Mercredi\Entity\Security\Traits\UserAddTrait;
use AcMarche\Mercredi\Entity\Traits\AccueilsTrait;
use AcMarche\Mercredi\Entity\Traits\AnneeScolaireTrait;
use AcMarche\Mercredi\Entity\Traits\ArchiveTrait;
use AcMarche\Mercredi\Entity\Traits\BirthdayTrait;
use AcMarche\Mercredi\Entity\Traits\EcoleTrait;
use AcMarche\Mercredi\Entity\Traits\EnfantNotesTrait;
use AcMarche\Mercredi\Entity\Traits\GroupeScolaireTrait;
use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\IsAccueilEcoleTrait;
use AcMarche\Mercredi\Entity\Traits\NomTrait;
use AcMarche\Mercredi\Entity\Traits\OrdreTrait;
use AcMarche\Mercredi\Entity\Traits\PhotoAutorisationTrait;
use AcMarche\Mercredi\Entity\Traits\PhotoTrait;
use AcMarche\Mercredi\Entity\Traits\PoidsTrait;
use AcMarche\Mercredi\Entity\Traits\PrenomTrait;
use AcMarche\Mercredi\Entity\Traits\PresencesTrait;
use AcMarche\Mercredi\Entity\Traits\RegistreNationalTrait;
use AcMarche\Mercredi\Entity\Traits\RelationsTrait;
use AcMarche\Mercredi\Entity\Traits\RemarqueTrait;
use AcMarche\Mercredi\Entity\Traits\SexeTrait;
use AcMarche\Mercredi\Entity\Traits\TelephonesTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\UuidableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Knp\DoctrineBehaviors\Model\Uuidable\UuidableTrait;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity()
 * @Vich\Uploadable
 */
class Enfant implements SluggableInterface, TimestampableInterface, UuidableInterface
{
    use IdTrait,
        NomTrait,
        PrenomTrait,
        BirthdayTrait,
        SexeTrait,
        PhotoAutorisationTrait,
        RemarqueTrait,
        OrdreTrait,
        PhotoTrait,
        UserAddTrait,
        SluggableTrait,
        EcoleTrait,
        RelationsTrait,
        ArchiveTrait,
        TimestampableTrait,
        TelephonesTrait,
        SanteFicheTrait,
        FicheSanteIsCompleteTrait,
        UuidableTrait,
        GroupeScolaireTrait,
        AnneeScolaireTrait,
        PresencesTrait,
        AccueilsTrait,
        EnfantNotesTrait,
        IsAccueilEcoleTrait,
        RegistreNationalTrait,
        PoidsTrait;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private bool $photo_autorisation;

    /**
     * @ORM\ManyToOne(targetEntity=AnneeScolaire::class, inversedBy="enfants")
     */
    private ?AnneeScolaire $annee_scolaire = null;

    /**
     * @ORM\ManyToOne(targetEntity=GroupeScolaire::class, inversedBy="enfants")
     */
    private ?GroupeScolaire $groupe_scolaire = null;

    /**
     * @ORM\ManyToOne(targetEntity=Ecole::class, inversedBy="enfants")
     */
    private ?Ecole $ecole = null;

    /**
     * @var Relation[]
     * @ORM\OneToMany(targetEntity=Relation::class, mappedBy="enfant", cascade={"remove"})
     */
    private iterable $relations;

    /**
     * J'ai mis la definition pour pouvoir mettre le cascade.
     *
     * @var Presence[]
     * @ORM\OneToMany(targetEntity=Presence::class, mappedBy="enfant", cascade={"remove"})
     */
    private iterable $presences;

    /**
     * J'ai mis la definition pour pouvoir mettre le cascade.
     *
     * @var Accueil[]
     * @ORM\OneToMany(targetEntity=Accueil::class, mappedBy="enfant", cascade={"remove"})
     */
    private iterable $accueils;

    public function __construct()
    {
        $this->relations = new ArrayCollection();
        $this->accueils = new ArrayCollection();
        $this->presences = new ArrayCollection();
        $this->ficheSanteIsComplete = false;
        $this->notes = new ArrayCollection();
        $this->photo_autorisation = false;
    }

    public function __toString()
    {
        return mb_strtoupper($this->nom, 'UTF-8') . ' ' . $this->prenom;
    }

    public function getSluggableFields(): array
    {
        return ['nom', 'prenom'];
    }

    public function shouldGenerateUniqueSlugs(): bool
    {
        return true;
    }

    public function getTuteurs(): array
    {
        return array_map(
            fn ($relation) => $relation->getTuteur(),
            $this->getRelations()->toArray()
        );
    }
}
