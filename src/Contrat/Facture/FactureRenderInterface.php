<?php

namespace AcMarche\Mercredi\Contrat\Facture;

use AcMarche\Mercredi\Facture\FactureInterface;

interface FactureRenderInterface
{
    /**
     * Render html details to show the facture
     * @param FactureInterface $facture
     * @return string
     */
    public function render(FactureInterface $facture): string;
}
