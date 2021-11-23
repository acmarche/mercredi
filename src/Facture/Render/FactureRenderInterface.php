<?php

namespace AcMarche\Mercredi\Facture\Render;

use AcMarche\Mercredi\Facture\FactureInterface;

interface FactureRenderInterface
{
    public function render(FactureInterface $facture):string;
}
