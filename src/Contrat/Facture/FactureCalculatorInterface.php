<?php

namespace AcMarche\Mercredi\Contrat\Facture;

use AcMarche\Mercredi\Entity\Presence\Accueil;
use AcMarche\Mercredi\Entity\Presence\Presence;
use AcMarche\Mercredi\Facture\Dto\FactureDetailDto;
use AcMarche\Mercredi\Facture\FactureInterface;

interface FactureCalculatorInterface
{
    public function createDetail(FactureInterface $facture): FactureDetailDto;

    /**
     * Total de la facture, complements et reductions compris.
     */
    public function total(FactureInterface $facture): float;

    /**
     * Total.
     */
    public function totalPresences(FactureInterface $facture): float;

    /**
     * Total.
     */
    public function totalAccueils(FactureInterface $facture): float;

    /**
     * Montant total des montants fixes.
     */
    public function totalReductionAmounts(FactureInterface $facture): float;

    /**
     * Total des pourcentages a appliquer sur le total de la facture.
     */
    public function totalReductionPourcentage(FactureInterface $facture): float;

    /**
     * Montant total des  montants fixes.
     */
    public function totalComplementAmounts(FactureInterface $facture): float;

    /**
     * Total des pourcentages a appliquer sur le total de la facture.
     */
    public function totalComplementPourcentage(FactureInterface $facture): float;

    public function isPresencePaid(Presence $presence): bool;
    public function isAccueilPaid(Accueil $accueil): bool;
}
