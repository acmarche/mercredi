<?php

namespace AcMarche\Mercredi\Presence\Calculator;

use AcMarche\Mercredi\Data\MercrediConstantes;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Presence\Entity\PresenceInterface;
use AcMarche\Mercredi\Reduction\Calculator\ReductionCalculator;
use AcMarche\Mercredi\Relation\Utils\OrdreService;

final class PrenceHottonCalculator implements PresenceCalculatorInterface
{
    /**
     * @var OrdreService
     */
    private $ordreService;
    /**
     * @var ReductionCalculator
     */
    private $reductionCalculator;

    public function __construct(
        OrdreService $ordreService,
        ReductionCalculator $reductionCalculator
    ) {
        $this->ordreService = $ordreService;
        $this->reductionCalculator = $reductionCalculator;
    }

    public function calculate(PresenceInterface $presence): float
    {
        /*
         * Absence.avec certificat
         */
        if (MercrediConstantes::ABSENCE_AVEC_CERTIF === $presence->getAbsent()) {
            return 0;
        }
        $jour = $presence->getJour();
        if ($jour->getPlaineJour() !== null) {
            return $this->calculatePlaine($presence, $jour);
        }
        if ($jour->isPedagogique()) {
            return $this->calculatePedagogique($presence, $jour);
        }

        return $this->calculatePresence($presence, $jour);
    }

    private function calculatePresence(PresenceInterface $presence, Jour $jour): float
    {
        $ordre = $this->ordreService->getOrdreOnPresence($presence);
        $prix = $this->getPrixByOrdre($jour, $ordre);

        return $this->reductionApplicate($presence, $prix);
    }

    private function calculatePedagogique(PresenceInterface $presence, Jour $jour): float
    {
        $prix = $presence->isHalf() ? $jour->getPrix2() : $jour->getPrix1();

        return $this->reductionApplicate($presence, $prix);
    }

    private function calculatePlaine(PresenceInterface $presence, Jour $jour): float
    {
        $plaineJour = $jour->getPlaineJour();
        $plaine = $plaineJour->getPlaine();
        $ordre = $this->ordreService->getOrdreOnPresence($presence);
        $prix = $plaine->getPrix1();

        if ($ordre > 1) {
            $prix = $plaine->getPrix1();
        }

        return $this->reductionApplicate($presence, $prix);
    }

    private function reductionApplicate(PresenceInterface $presence, float $cout)
    {
        if (($reduction = $presence->getReduction()) !== null) {
            return $this->reductionCalculator->applicate($reduction, $cout);
        }

        return $cout;
    }

    private function getPrixByOrdre(Jour $jour, $ordre)
    {
        switch ($ordre) {
            case 2:
                return $jour->getPrix2();
            case 3:
                return $jour->getPrix3();
            default:
                return $jour->getPrix1();
        }
    }
}
