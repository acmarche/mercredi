<?php

namespace AcMarche\Mercredi\Reduction\Calculator;

use AcMarche\Mercredi\Entity\Reduction;

final class ReductionCalculator
{
    public function applicate(Reduction $reduction, float $montant): float
    {
        if ($forfait = $reduction->getForfait()) {
            return $montant - $forfait;
        }
        if ($pourcentage = $reduction->getPourcentage()) {
            return $montant - ($montant / 100 * $pourcentage);
        }

        return $montant;
    }
}
