<?php

namespace AcMarche\Mercredi\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait ShortNameTrait
{
    #[ORM\Column(type: 'string', length: 150, nullable: true)]
    public ?string $short_name = null;
}
