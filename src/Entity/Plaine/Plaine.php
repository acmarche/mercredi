<?php

namespace AcMarche\Mercredi\Entity\Plaine;

use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\NomTrait;
use AcMarche\Mercredi\Entity\Traits\PrixTrait;
use AcMarche\Mercredi\Entity\Traits\RemarqueTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Plaine\Repository\PlaineRepository")
 */
class Plaine
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

    /**
     * @var PlaineJour[]
     * @ORM\OneToMany(targetEntity="AcMarche\Mercredi\Entity\Plaine\PlaineJour", mappedBy="plaine", cascade={"remove"})
     */
    private $plaine_jours;

    /**
     * @var PlaineGroupe[]|null
     * @ORM\OneToMany(targetEntity="AcMarche\Mercredi\Entity\Plaine\PlaineGroupe", mappedBy="plaine", cascade={"remove","persist"})
     */
    private $plaine_groupes;

    public function __construct()
    {
        $this->jours = new ArrayCollection();
        $this->plaine_groupes = new ArrayCollection();
        $this->plaine_jours = new ArrayCollection();
        $this->inscriptionOpen = false;
        $this->prix1 = 0;
        $this->prix2 = 0;
        $this->prix3 = 0;
        //  $this->plaine_jours = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->nom;
    }
}
