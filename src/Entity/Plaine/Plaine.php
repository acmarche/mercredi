<?php

namespace AcMarche\Mercredi\Entity\Plaine;

use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Entity\Facture\FacturesTrait;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Entity\Scolaire\GroupeScolaire;
use AcMarche\Mercredi\Entity\Traits\ArchiveTrait;
use AcMarche\Mercredi\Entity\Traits\CommunicationTrait;
use AcMarche\Mercredi\Entity\Traits\IdOldTrait;
use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\NomTrait;
use AcMarche\Mercredi\Entity\Traits\PrixTrait;
use AcMarche\Mercredi\Entity\Traits\RemarqueTrait;
use AcMarche\Mercredi\Plaine\Repository\PlaineRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Stringable;

#[ORM\Entity(repositoryClass: PlaineRepository::class)]
class Plaine implements SluggableInterface, Stringable
{
    use IdTrait;
    use NomTrait;
    use RemarqueTrait;
    use InscriptionOpenTrait;
    use PrixTrait;
    use PrematernelleTrait;
    use PlaineGroupesTrait;
    use SluggableTrait;
    use ArchiveTrait;
    use JoursTrait;
    use CommunicationTrait;
    use FacturesTrait;
    use IdOldTrait;
    public array $enfants = [];
    /**
     * @var Jour[]
     */
    #[ORM\OneToMany(mappedBy: 'plaine', targetEntity: Jour::class, cascade: ['remove'])]
    private Collection $jours;
    /**
     * @var PlaineGroupe[]|null
     */
    #[ORM\OneToMany(mappedBy: 'plaine', targetEntity: PlaineGroupe::class, cascade: ['remove', 'persist'])]
    private Collection $plaine_groupes;
    #[ORM\Column(type: 'string', length: 100, unique: false, nullable: true)]
    private ?string $communication = null;
    /**
     * @var Facture[]
     */
    #[ORM\OneToMany(mappedBy: 'plaine', targetEntity: Facture::class, cascade: ['remove'])]
    private Collection $factures;

    /**
     * @var array|GroupeScolaire[] $groupesScolaire
     */
    public array $groupesScolaire;

    public function __construct()
    {
        $this->jours = new ArrayCollection();
        $this->plaine_groupes = new ArrayCollection();
        $this->inscriptionOpen = false;
        $this->prix1 = 0;
        $this->prix2 = 0;
        $this->prix3 = 0;
    }

    public function __toString(): string
    {
        return $this->nom;
    }

    public function getSluggableFields(): array
    {
        return ['nom'];
    }

    public function shouldGenerateUniqueSlugs(): bool
    {
        return true;
    }

    public function getFirstDay(): Jour
    {
        return $this->jours[0];
    }
}
