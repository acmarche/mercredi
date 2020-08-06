<?php

namespace AcMarche\Mercredi\Entity;

use AcMarche\Mercredi\Entity\Traits\AdresseTrait;
use AcMarche\Mercredi\Entity\Traits\EmailTrait;
use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\NomTrait;
use AcMarche\Mercredi\Entity\Traits\PhotoTrait;
use AcMarche\Mercredi\Entity\Traits\RemarqueTrait;
use AcMarche\Mercredi\Entity\Traits\SiteWebTrait;
use AcMarche\Mercredi\Entity\Traits\TelephonieTrait;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity()
 * @Vich\Uploadable
 */
class Organisation
{
    use IdTrait;
    use NomTrait;
    use EmailTrait;
    use AdresseTrait;
    use SiteWebTrait;
    use TelephonieTrait;
    use RemarqueTrait;
    use PhotoTrait;

    public function __toString()
    {
        return $this->nom;
    }
}
