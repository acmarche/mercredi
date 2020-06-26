<?php

namespace AcMarche\Mercredi\Entity;

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

    /**
     * @var Jour[]
     * @ORM\ManyToMany(targetEntity="AcMarche\Mercredi\Entity\Jour")
     */
    private $jours;

    public function __construct()
    {
        $this->jours = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->nom;
    }


}
