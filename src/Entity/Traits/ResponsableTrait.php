<?php

namespace AcMarche\Mercredi\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait ResponsableTrait
{
    #[ORM\Column(type: 'string', length: 150, nullable: true)]
    public ?string $responsable_nom = null;
    #[ORM\Column(type: 'string', length: 150, nullable: true)]
    public ?string $responsable_prenom = null;
    #[ORM\Column(type: 'string', length: 150, nullable: true)]
    public ?string $responsable_fonction = null;
}