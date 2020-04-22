<?php


namespace AcMarche\Mercredi\Entity;

use AcMarche\Mercredi\Entity\Traits\AdresseTrait;
use AcMarche\Mercredi\Entity\Traits\EmailTrait;
use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\NomTrait;
use AcMarche\Mercredi\Entity\Traits\RemarqueTrait;
use AcMarche\Mercredi\Entity\Traits\TelephonieTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Ecole\Repository\EcoleRepository")
 */
class Ecole
{
    use IdTrait,
        NomTrait,
        AdresseTrait,
        TelephonieTrait,
        EmailTrait,
        RemarqueTrait;

    public function __toString()
    {
        return $this->nom;
    }
}
