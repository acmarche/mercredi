<?php

namespace AcMarche\Mercredi\Entity\Plaine;

use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\JoursTrait;
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
    use JoursTrait;
    use InscriptionOpen;
    use PrixTrait;

    /**
     * @var Jour[]
     * @ORM\OneToMany(targetEntity="AcMarche\Mercredi\Entity\Jour", mappedBy="plaine", cascade={"remove"})
     */
    private $jours;

    public function __construct()
    {
        $this->jours = new ArrayCollection();
        $this->inscriptionOpen = false;
        $this->prix1 = 0;
        $this->prix2 = 0;
        $this->prix3 = 0;
    }

    public function __toString()
    {
        return $this->nom;
    }
}
