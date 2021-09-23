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
            return $montant - $this->calculatePourcentage($montant, $pourcentage);
        }

        return $montant;
    }

    public function calculatePourcentage(float $montant, float $pourcentage):float {
       return ($montant / 100 * $pourcentage);
    }
}
