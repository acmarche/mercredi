<?php

namespace AcMarche\Mercredi\Entity;

use AcMarche\Mercredi\Entity\Facture\CreancesTrait;
use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Entity\Facture\FacturesTrait;
use AcMarche\Mercredi\Entity\Presence\Accueil;
use AcMarche\Mercredi\Entity\Security\Traits\UserAddTrait;
use AcMarche\Mercredi\Entity\Security\Traits\UsersTrait;
use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Entity\Traits\AccueilsTraits;
use AcMarche\Mercredi\Entity\Traits\AdresseTrait;
use AcMarche\Mercredi\Entity\Traits\ArchiveTrait;
use AcMarche\Mercredi\Entity\Traits\ConjointTrait;
use AcMarche\Mercredi\Entity\Traits\EmailTrait;
use AcMarche\Mercredi\Entity\Traits\IbanTrait;
use AcMarche\Mercredi\Entity\Traits\IdOldTrait;
use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\NomTrait;
use AcMarche\Mercredi\Entity\Traits\PapierTrait;
use AcMarche\Mercredi\Entity\Traits\PrenomTrait;
use AcMarche\Mercredi\Entity\Traits\PresencesTuteurTrait;
use AcMarche\Mercredi\Entity\Traits\RelationsTrait;
use AcMarche\Mercredi\Entity\Traits\RemarqueTrait;
use AcMarche\Mercredi\Entity\Traits\SexeTrait;
use AcMarche\Mercredi\Entity\Traits\TelephonieTrait;
use AcMarche\Mercredi\Tuteur\Repository\TuteurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Stringable;

#[ORM\Entity(repositoryClass: TuteurRepository::class)]
class Tuteur implements SluggableInterface, TimestampableInterface, Stringable
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
    use PapierTrait;
    use IbanTrait;
    use CreancesTrait;
    use IdOldTrait;

    /**
     * @var Relation[]
     */
    #[ORM\OneToMany(targetEntity: Relation::class, mappedBy: 'tuteur', cascade: ['remove'])]
    private Collection $relations;
    /**
     * J'ai mis la definition pour pouvoir mettre le cascade.
     *
     * @var Accueil[]|Collection
     */
    #[ORM\OneToMany(targetEntity: Accueil::class, mappedBy: 'tuteur', cascade: ['remove'])]
    private Collection $accueils;
    /**
     * @var Facture[]
     */
    #[ORM\OneToMany(targetEntity: Facture::class, mappedBy: 'tuteur', cascade: ['remove'])]
    private Collection $factures;
    /**
     * @var User[]|Collection
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'tuteurs')]
    private Collection $users;

    public bool $createAccount = false;

    public function __construct()
    {
        $this->relations = new ArrayCollection();
        $this->presences = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->accueils = new ArrayCollection();
    }

    public function __toString(): string
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
