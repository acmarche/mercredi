<?php

namespace AcMarche\Mercredi\Entity;

use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\NomTrait;
use AcMarche\Mercredi\Entity\Traits\RemarqueTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class GroupeScolaire
{
    use IdTrait;
    use NomTrait;
    use RemarqueTrait;

    public function __toString()
    {
        return $this->nom;
    }
}
