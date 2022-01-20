<?php

namespace AcMarche\Mercredi\Entity\Facture;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

trait CreancesTrait
{
    /**
     * @var Collection|Creance[]
     */
    #[ORM\OneToMany(targetEntity: 'AcMarche\Mercredi\Entity\Facture\Creance', mappedBy: 'tuteur', cascade: ['remove'])]
    private Collection $creances;

    /**
     * @return Creance[]|Collection
     */
    public function getCreances(): Collection
    {
        return $this->creances;
    }

    /**
     * @param Creance[]|Collection $creances
     */
    public function setCreances(Collection $creances): void
    {
        $this->creances = $creances;
    }
}
