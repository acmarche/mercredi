<?php

namespace AcMarche\Mercredi\Plaine\Calculator;

use AcMarche\Mercredi\Data\MercrediConstantes;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Entity\Presence;
use AcMarche\Mercredi\Plaine\Handler\PlainePresenceHandler;
use AcMarche\Mercredi\Presence\Entity\PresenceInterface;
use AcMarche\Mercredi\Reduction\Calculator\ReductionCalculator;
use AcMarche\Mercredi\Relation\Utils\OrdreService;

final class PlaineHottonCalculator implements PlaineCalculatorInterface
{
    private PlainePresenceHandler $plainePresenceHandler;
    private OrdreService $ordreService;
    private ReductionCalculator $reductionCalculator;

    public function __construct(
        PlainePresenceHandler $plainePresenceHandler,
        OrdreService $ordreService,
        ReductionCalculator $reductionCalculator
    ) {
        $this->plainePresenceHandler = $plainePresenceHandler;
        $this->ordreService = $ordreService;
        $this->reductionCalculator = $reductionCalculator;
    }

    /**
     * @param \AcMarche\Mercredi\Entity\Plaine\Plaine $plaine
     * @param array|PresenceInterface[] $presences
     * @return float
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
        $ordre = $this->ordreService->getOrdreOnPresence($presence);
        $prix = $this->getPrixByOrdre($plaine, $ordre);
        $cout = $this->applicateReduction($presence, $prix);

        return $cout;
    }

    private function applicateReduction(PresenceInterface $presence, float $cout): float
    {
        if (null !== ($reduction = $presence->getReduction())) {
            return $this->reductionCalculator->applicate($reduction, $cout);
        }

        return $cout;
    }

    private function getPrixByOrdre(Plaine $plaine, $ordre): float
    {
        if ($ordre > 1) {
            return $plaine->getPrix2();
        }

        return $plaine->getPrix1();
    }
}
