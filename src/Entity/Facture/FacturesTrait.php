<?php

namespace AcMarche\Mercredi\Entity\Facture;

use Doctrine\Common\Collections\Collection;

trait FacturesTrait
{
    /**
     * @var Facture[]
     */
    private array|Collection $factures;

    /**
     * @return Facture[]
     */
    public function getFactures(): array|Collection
    {
        return $this->factures;
    }

    /**
     * @param Facture[] $factures
     */
    public function setFactures(Collection $factures): void
    {
        $this->factures = $factures;
    }
}
