<?php

namespace AcMarche\Mercredi\Entity;

use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Entity\Facture\FacturesTrait;
use AcMarche\Mercredi\Entity\Security\Traits\UserAddTrait;
use AcMarche\Mercredi\Entity\Security\Traits\UsersTrait;
use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Entity\Traits\AccueilsTraits;
use AcMarche\Mercredi\Entity\Traits\AdresseTrait;
use AcMarche\Mercredi\Entity\Traits\ArchiveTrait;
use AcMarche\Mercredi\Entity\Traits\ConjointTrait;
use AcMarche\Mercredi\Entity\Traits\EmailTrait;
use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\NomTrait;
use AcMarche\Mercredi\Entity\Traits\PrenomTrait;
use AcMarche\Mercredi\Entity\Traits\PresencesTuteurTrait;
use AcMarche\Mercredi\Entity\Traits\RelationsTrait;
use AcMarche\Mercredi\Entity\Traits\RemarqueTrait;
use AcMarche\Mercredi\Entity\Traits\SexeTrait;
use AcMarche\Mercredi\Entity\Traits\TelephonieTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Tuteur\Repository\TuteurRepository")
 */
class Tuteur implements SluggableInterface, TimestampableInterface
{
    use IdTrait;
    use NomTrait;
    use PrenomTrait;
    use AdresseTrait;
    use EmailTrait;
    use ConjointTrait;
    use RemarqueTrait;
    use SexeTrait;
    use TelephonieTrait;
    use SluggableTrait;
    use ArchiveTrait;
    use TimestampableTrait;
    use UserAddTrait;
    use UsersTrait;
    use PresencesTuteurTrait;
    use RelationsTrait;
    use FacturesTrait;
    use AccueilsTraits;

    /**
     * @var Relation[]
     * @ORM\OneToMany(targetEntity="AcMarche\Mercredi\Entity\Relation", mappedBy="tuteur", cascade={"remove"})
     */
    private $relations;

    /**
     * J'ai mis la definition pour pouvoir mettre le cascade.
     *
     * @var Accueil[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="AcMarche\Mercredi\Entity\Accueil", mappedBy="tuteur", cascade={"remove"})
     */
    private $accueils;

    /**
     * @var Facture[]
     * @ORM\OneToMany(targetEntity="AcMarche\Mercredi\Entity\Facture\Facture", mappedBy="tuteur")
     */
    private $factures;

    /**
     * @ORM\ManyToMany(targetEntity="AcMarche\Mercredi\Entity\Security\User", mappedBy="tuteurs" )
     *
     * @var User[]|Collection
     */
    private $users;

    public function __construct()
    {
        $this->relations = [];
        $this->presences = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->accueils = new ArrayCollection();
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

}
