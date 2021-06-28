<?php

namespace AcMarche\Mercredi\Entity\Facture;

trait FacturesTrait
{
    /**
     * @var Facture[]
     */
    private iterable $factures;

    /**
     * @return Facture[]
     */
    public function getFactures(): array
    {
        return $this->factures;
    }

    /**
     * @param Facture[] $factures
     */
    public function setFactures(array $factures): void
    {
        $this->factures = $factures;
    }
}
