<?php

namespace AcMarche\Mercredi\Contrat\Facture;

use AcMarche\Mercredi\Facture\FactureInterface;

interface FacturePdfRenderInterface
{
    public function render(FactureInterface $facture): string;
}
