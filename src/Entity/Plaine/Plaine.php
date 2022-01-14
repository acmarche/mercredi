<?php

namespace AcMarche\Mercredi\Entity\Plaine;

use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Entity\Facture\FacturesTrait;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Entity\Traits\ArchiveTrait;
use AcMarche\Mercredi\Entity\Traits\CommunicationTrait;
use AcMarche\Mercredi\Entity\Traits\IdOldTrait;
use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\NomTrait;
use AcMarche\Mercredi\Entity\Traits\PrixTrait;
use AcMarche\Mercredi\Entity\Traits\RemarqueTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;

#[ORM\Entity(repositoryClass: 'AcMarche\Mercredi\Plaine\Repository\PlaineRepository')]
class Plaine implements SluggableInterface
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
    /**
     * @var Jour[]
     */
    #[ORM\OneToMany(targetEntity: Jour::class, mappedBy: 'plaine', cascade: ['remove'])]
    private iterable $jours;
    /**
     * @var PlaineGroupe[]|null
     */
    #[ORM\OneToMany(targetEntity: PlaineGroupe::class, mappedBy: 'plaine', cascade: ['remove', 'persist'])]
    private iterable $plaine_groupes;
    #[ORM\Column(type: 'string', length: 100, nullable: true, unique: false)]
    private ?string $communication = null;
    /**
     * @var Facture[]
     */
    #[ORM\OneToMany(targetEntity: Facture::class, mappedBy: 'plaine', cascade: ['remove'])]
    private iterable $factures;
    public array $enfants = [];

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
