<?php

namespace AcMarche\Mercredi\Entity\Plaine;

use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\JoursTrait;
use AcMarche\Mercredi\Entity\Traits\NomTrait;
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

    /**
     * @var Jour[]
     * @ORM\OneToMany(targetEntity="AcMarche\Mercredi\Entity\Jour", mappedBy="plaine", cascade={"persist", "remove"})
     */
    private $jours;

    public function __construct()
    {
        $this->jours = new ArrayCollection();
        $this->inscriptionOpen = false;
    }

    public function __toString()
    {
        return $this->nom;
    }


}
