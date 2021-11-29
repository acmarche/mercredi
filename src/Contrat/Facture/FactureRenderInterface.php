<?php

namespace AcMarche\Mercredi\Contrat\Facture;

use AcMarche\Mercredi\Facture\FactureInterface;

interface FactureRenderInterface
{
    public function render(FactureInterface $facture): string;
}
