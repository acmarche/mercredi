<?php

namespace AcMarche\Mercredi\Contrat\Facture;

use AcMarche\Mercredi\Facture\FactureInterface;

interface FacturePdfPlaineInterface
{
    public function render(FactureInterface $facture): string;
}
