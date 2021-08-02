<?php

namespace AcMarche\Mercredi\Entity\Plaine;

use Doctrine\Common\Collections\Collection;
use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\NomTrait;
use AcMarche\Mercredi\Entity\Traits\PrixTrait;
use AcMarche\Mercredi\Entity\Traits\RemarqueTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Plaine\Repository\PlaineRepository")
 */
class Plaine implements SluggableInterface
{
    use IdTrait;
    use NomTrait;
    use RemarqueTrait;
    use PlaineJoursTrait;
    use JoursTrait;
    use InscriptionOpenTrait;
    use PrixTrait;
    use PrematernelleTrait;
    use PlaineGroupesTrait;
    use SluggableTrait;

    /**
     * @var PlaineJour[]
     * @ORM\OneToMany(targetEntity=PlaineJour::class, mappedBy="plaine", cascade={"remove"})
     */
    private iterable $plaine_jours;

    /**
     * @var PlaineGroupe[]|null
     * @ORM\OneToMany(targetEntity=PlaineGroupe::class, mappedBy="plaine", cascade={"remove","persist"})
     */
    private iterable $plaine_groupes;

    public array $enfants = [];

    public function __construct()
    {
        $this->jours = new ArrayCollection();
        $this->plaine_groupes = new ArrayCollection();
        $this->plaine_jours = new ArrayCollection();
        $this->inscriptionOpen = false;
        $this->prix1 = 0;
        $this->prix2 = 0;
        $this->prix3 = 0;
    }

    public function __toString()
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
}
