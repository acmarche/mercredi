<?php

namespace AcMarche\Mercredi\Entity\Traits;

use AcMarche\Mercredi\Entity\Paiement;
use Doctrine\ORM\Mapping as ORM;

trait PaiementTrait
{
    #[ORM\ManyToOne(targetEntity: 'AcMarche\Mercredi\Entity\Paiement')]
    private ?Paiement $paiement = null;

    public function getPaiement(): ?Paiement
    {
        return $this->paiement;
    }

    public function setPaiement(?Paiement $paiement): void
    {
        $this->paiement = $paiement;
    }
}
