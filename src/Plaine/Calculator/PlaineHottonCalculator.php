<?php

namespace AcMarche\Mercredi\Plaine\Calculator;

use AcMarche\Mercredi\Data\MercrediConstantes;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Plaine\Handler\PlainePresenceHandler;
use AcMarche\Mercredi\Presence\Entity\PresenceInterface;
use AcMarche\Mercredi\Reduction\Calculator\ReductionCalculator;
use AcMarche\Mercredi\Relation\Utils\OrdreService;

final class PlaineHottonCalculator implements PlaineCalculatorInterface
{
    /**
     * @var PlainePresenceHandler
     */
    private $plainePresenceHandler;
    /**
     * @var OrdreService
     */
    private $ordreService;
    /**
     * @var ReductionCalculator
     */
    private $reductionCalculator;

    public function __construct(
        PlainePresenceHandler $plainePresenceHandler,
        OrdreService $ordreService,
        ReductionCalculator $reductionCalculator
    ) {
        $this->plainePresenceHandler = $plainePresenceHandler;
        $this->ordreService = $ordreService;
        $this->reductionCalculator = $reductionCalculator;
    }

    public function calculate(Plaine $plaine, Enfant $enfant): float
    {
        $presences = $this->plainePresenceHandler->findPresencesByPlaineEnfant($plaine, $enfant);
        $total = 0;
        foreach ($presences as $presence) {
            if (MercrediConstantes::ABSENCE_AVEC_CERTIF === $presence->getAbsent()) {
                continue;
            }

            $ordre = $this->ordreService->getOrdreOnPresence($presence);
            $prix = $this->getPrixByOrdre($plaine, $ordre);
            $cout = $this->reductionApplicate($presence, $prix);

            $total += $cout;
        }

        return $total;
    }

    private function reductionApplicate(PresenceInterface $presence, float $cout)
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
