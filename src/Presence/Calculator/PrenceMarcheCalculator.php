<?php

namespace AcMarche\Mercredi\Presence\Calculator;

use AcMarche\Mercredi\Contrat\Presence\PresenceCalculatorInterface;
use AcMarche\Mercredi\Contrat\Presence\PresenceInterface;
use AcMarche\Mercredi\Data\MercrediConstantes;
use AcMarche\Mercredi\Entity\Facture\FacturePresence;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Reduction\Calculator\ReductionCalculator;
use AcMarche\Mercredi\Relation\Utils\OrdreService;

final class PrenceMarcheCalculator implements PresenceCalculatorInterface
{
    public array $ecoles = [];
    public string $ordre_raison = '';

    public function __construct(
        private OrdreService $ordreService,
        private ReductionCalculator $reductionCalculator
    ) {
    }

    public function calculate(PresenceInterface $presence): float
    {
        /**
         * Absence.avec certificat
         */
        if (MercrediConstantes::ABSENCE_AVEC_CERTIF === $presence->getAbsent()) {
            return 0;
        }

        if (null !== ($reduction = $presence->getReduction())) {
            if ($reduction->is_forfait === true) {
                return $reduction->amount;
            }
        }

        $jour = $presence->getJour();
        if (null !== $jour->getPlaine()) {
            return $this->calculatePlaine($presence, $jour);
        }

        return $this->calculatePresence($presence);
    }

    public function getOrdreOnPresence(PresenceInterface $presence): int
    {
        $ordre = $this->ordreService->getOrdreOnPresence($presence);
        $this->ordre_raison = $this->ordreService->raison;

        return $ordre;
    }

    public function getPrixByOrdre(PresenceInterface $presence, int $ordre): float
    {
        $jour = $presence->getJour();
        if ($ordre >= 3) {
            return $jour->getPrix3();
        }
        if (2 === $ordre) {
            return $jour->getPrix2();
        }

        return $jour->getPrix1();
    }

    private function calculatePresence(PresenceInterface $presence): float
    {
        $ordre = $this->ordreService->getOrdreOnPresence($presence);
        $prix = $this->getPrixByOrdre($presence, $ordre);

        return $this->reductionApplicate($presence, $prix);
    }

    private function calculatePlaine(PresenceInterface $presence, Jour $jour): float
    {
        $plaine = $jour->getPlaine();
        $ordre = $this->getOrdreOnPresence($presence);

        $prix = match ($ordre) {
            2 => $plaine->getPrix2(),
            3 => $plaine->getPrix3(),
            default => $plaine->getPrix1(),
        };

        return $this->reductionApplicate($presence, $prix);
    }

    private function reductionApplicate(PresenceInterface $presence, float $cout): float
    {
        if (null !== ($reduction = $presence->getReduction())) {
            return $this->reductionCalculator->applicate($reduction, $cout);
        }

        return $cout;
    }
}
