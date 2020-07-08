<?php

namespace AcMarche\Mercredi\Entity\Facture;

trait FactureTrait
{
    /**
     * @var Facture
     */
    private $facture;

    public function getFacture(): Facture
    {
        return $this->facture;
    }

    public function setFacture(Facture $facture): void
    {
        $this->facture = $facture;
    }
}
