<?php

namespace AcMarche\Mercredi\Entity\Facture;

use Doctrine\ORM\Mapping as ORM;

trait CreancesTrait
{
    /**
     * @var iterable|Creance[]
     * @ORM\OneToMany(targetEntity="AcMarche\Mercredi\Entity\Facture\Creance", mappedBy="tuteur", cascade={"remove"})
     */
    private iterable $creances = [];

    /**
     * @return Creance[]|iterable
     */
    public function getCreances(): iterable
    {
        return $this->creances;
    }

    /**
     * @param Creance[]|iterable $creances
     */
    public function setCreances(iterable $creances): void
    {
        $this->creances = $creances;
    }
}
