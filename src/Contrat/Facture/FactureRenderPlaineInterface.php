<?php

namespace AcMarche\Mercredi\Contrat\Facture;

use AcMarche\Mercredi\Facture\FactureInterface;

interface FactureRenderPlaineInterface
{
    public function renderForDetail(FactureInterface $facture):string;

    public function renderForPdf(FactureInterface $facture):string;
}
