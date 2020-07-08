<?php

namespace AcMarche\Mercredi\Entity\Sante\Traits;

use AcMarche\Mercredi\Entity\Sante\SanteFiche;
use Doctrine\ORM\Mapping as ORM;

trait SanteFicheTrait
{
    /**
     * @var SanteFiche|null
     * @ORM\OneToOne(targetEntity="AcMarche\Mercredi\Entity\Sante\SanteFiche", mappedBy="enfant", cascade={"remove"})
     */
    private $sante_fiche;

    public function getSanteFiche(): ?SanteFiche
    {
        return $this->sante_fiche;
    }

    public function setSanteFiche(?SanteFiche $sante_fiche): void
    {
        $this->sante_fiche = $sante_fiche;
    }
}
