<?php

namespace AcMarche\Mercredi\Contrat\Facture;

use AcMarche\Mercredi\Facture\FactureInterface;

interface FacturePdfPresenceInterface
{
    public function render(FactureInterface $facture): string;

    /**
     * Utile pour impressions papier.
     *
     * @param array|FactureInterface[] $factures
     */
    public function renderMultiple(array $factures): string;
}
