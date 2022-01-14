<?php

namespace AcMarche\Mercredi\Plaine\Calculator;

use AcMarche\Mercredi\Contrat\Plaine\PlaineCalculatorInterface;
use AcMarche\Mercredi\Contrat\Presence\PresenceInterface;
use AcMarche\Mercredi\Data\MercrediConstantes;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Reduction\Calculator\ReductionCalculator;
use AcMarche\Mercredi\Relation\Utils\OrdreService;

final class PlaineHottonCalculator implements PlaineCalculatorInterface
{
    private OrdreService $ordreService;
    private ReductionCalculator $reductionCalculator;

    public function __construct(
        OrdreService $ordreService,
        ReductionCalculator $reductionCalculator
    ) {
        $this->ordreService = $ordreService;
        $this->reductionCalculator = $reductionCalculator;
    }

    /**
     * @param array|PresenceInterface[] $presences
     */
    public function calculate(Plaine $plaine, array $presences): float
    {
        $total = 0;
        foreach ($presences as $presence) {
            $cout = $this->calculateOnePresence($plaine, $presence);
            $total += $cout;
        }

        return $total;
    }

    public function calculateOnePresence(Plaine $plaine, PresenceInterface $presence): float
    {
        if (MercrediConstantes::ABSENCE_AVEC_CERTIF === $presence->getAbsent()) {
            return 0;
        }
        $ordre = $this->getOrdreOnePresence($presence);
        $prix = $this->getPrixByOrdre($plaine, $ordre);

        return $this->applicateReduction($presence, $prix);
    }

    public function getOrdreOnePresence(PresenceInterface $presence): int
    {
        return $this->ordreService->getOrdreOnPresence($presence);
    }

    private function applicateReduction(PresenceInterface $presence, float $cout): float
    {
        if (null !== ($reduction = $presence->getReduction())) {
            return $this->reductionCalculator->applicate($reduction, $cout);
        }

        return $cout;
    }

    public function getPrixByOrdre(Plaine $plaine, $ordre): float
    {
        if ($ordre > 1) {
            return $plaine->getPrix2();
        }

        return $plaine->getPrix1();
    }
}
