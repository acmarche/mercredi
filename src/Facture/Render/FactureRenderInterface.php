<?php

namespace AcMarche\Mercredi\Facture\Render;

use AcMarche\Mercredi\Facture\FactureInterface;

interface FactureRenderInterface
{
    public function renderForDetails(FactureInterface $facture):string;
    public function renderForPdf(FactureInterface $facture):string;
}
