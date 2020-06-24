<?php


namespace AcMarche\Mercredi\Entity\Facture;


trait FactureTrait
{
    /**
     * @var Facture
     */
    private $facture;

    /**
     * @return Facture
     */
    public function getFacture(): Facture
    {
        return $this->facture;
    }

    /**
     * @param Facture $facture
     */
    public function setFacture(Facture $facture): void
    {
        $this->facture = $facture;
    }


}
