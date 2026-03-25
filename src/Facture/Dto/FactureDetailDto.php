<?php

namespace AcMarche\Mercredi\Facture\Dto;

class FactureDetailDto
{
    public float $totalPresences = 0;
    public float $totalAccueils = 0;
    public float $totalPlaines = 0;
    /**
     * $totalReductionAmounts is the amount of the discount applied
     */
    public float $totalReductionAmounts = 0;

    /**
     * $totalComplementAmounts is the amount of the complement applied
     */
    public float $totalComplementAmounts = 0;
    /**
     * $totalReductionPourcentage is the percentage of the reduction to apply on $totalHorsPourcentage
     */
    public float $totalReductionPourcentage = 0;
    /**
     * $totalComplementPourcentage is the percentage of the complement to apply on $totalHorsPourcentage
     */
    public float $totalComplementPourcentage = 0;
    /**
     * $pourcentageEnPlus is the amount of the complement to be applied
     */
    public float $pourcentageEnPlus = 0;
    /**
     * $pourcentageEnMoins is the amount of the discount to be applied
     */
    public float $pourcentageEnMoins = 0;

    public float $totalDecomptes = 0;
    /**
     * $total is the total amount to be paid
     */
    public float $total = 0;
    /**
     * $totalDu is the remaining amount to be paid
     */
    public float $totalDu = 0;
    public float $totalHorsPourcentage = 0;
}
