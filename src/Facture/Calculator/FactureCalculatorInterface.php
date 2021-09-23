<?php

namespace AcMarche\Mercredi\Facture\Calculator;

use AcMarche\Mercredi\Facture\Dto\FactureDetailDto;
use AcMarche\Mercredi\Facture\FactureInterface;

interface FactureCalculatorInterface
{
    public function createDetail(FactureInterface $facture): FactureDetailDto;

    /**
     * Total de la facture, complements et reductions compris
     * @param \AcMarche\Mercredi\Facture\FactureInterface $facture
     * @return float
     */
    public function total(FactureInterface $facture): float;

    /**
     * Total
     * @param \AcMarche\Mercredi\Facture\FactureInterface $facture
     * @return float
     */
    public function totalPresences(FactureInterface $facture): float;

    /**
     * Total
     * @param \AcMarche\Mercredi\Facture\FactureInterface $facture
     * @return float
     */
    public function totalAccueils(FactureInterface $facture): float;

    /**
     * Montant total des forfaits
     * @param \AcMarche\Mercredi\Facture\FactureInterface $facture
     * @return float
     */
    public function totalReductionForfaits(FactureInterface $facture): float;

    /**
     * Total des pourcentages a appliquer sur le total de la facture
     * @param \AcMarche\Mercredi\Facture\FactureInterface $facture
     * @return float
     */
    public function totalReductionPourcentage(FactureInterface $facture): float;

    /**
     * Montant total des forfaits
     * @param \AcMarche\Mercredi\Facture\FactureInterface $facture
     * @return float
     */
    public function totalComplementForfaits(FactureInterface $facture): float;

    /**
     * Total des pourcentages a appliquer sur le total de la facture
     * @param \AcMarche\Mercredi\Facture\FactureInterface $facture
     * @return float
     */
    public function totalComplementPourcentage(FactureInterface $facture): float;

}
