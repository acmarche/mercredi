<?php

namespace AcMarche\Mercredi\Entity;

use AcMarche\Mercredi\Entity\Security\Traits\UsersTrait;
use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Entity\Traits\AdresseTrait;
use AcMarche\Mercredi\Entity\Traits\EmailTrait;
use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\NomTrait;
use AcMarche\Mercredi\Entity\Traits\RemarqueTrait;
use AcMarche\Mercredi\Entity\Traits\TelephonieTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Ecole\Repository\EcoleRepository")
 */
class Ecole
{
    use IdTrait;
    use NomTrait;
    use AdresseTrait;
    use TelephonieTrait;
    use EmailTrait;
    use RemarqueTrait;
    use UsersTrait;

    /**
     * Override pour le mappedBy
     * @ORM\ManyToMany(targetEntity="AcMarche\Mercredi\Entity\Security\User", mappedBy="ecoles" )
     *
     * @var User[]|Collection
     */
    private $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->nom;
    }
}
