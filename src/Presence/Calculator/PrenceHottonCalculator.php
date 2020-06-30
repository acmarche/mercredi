<?php

namespace AcMarche\Mercredi\Presence\Calculator;

use AcMarche\Mercredi\Data\MercrediConstantes;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Presence\Entity\PresenceInterface;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Reduction\Calculator\ReductionCalculator;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;
use AcMarche\Mercredi\Relation\Utils\OrdreService;

class PrenceHottonCalculator implements PresenceCalculatorInterface
{
    /**
     * @var OrdreService
     */
    private $ordreService;
    /**
     * @var ReductionCalculator
     */
    private $reductionCalculator;
    /**
     * @var RelationRepository
     */
    private $relationRepository;
    /**
     * @var PresenceRepository
     */
    private $presenceRepository;

    public function __construct(
        OrdreService $ordreService,
        ReductionCalculator $reductionCalculator,
        RelationRepository $relationRepository,
        PresenceRepository $presenceRepository
    ) {
        $this->ordreService = $ordreService;
        $this->reductionCalculator = $reductionCalculator;
        $this->relationRepository = $relationRepository;
        $this->presenceRepository = $presenceRepository;
    }

    public function calculate(PresenceInterface $presence): float
    {
        /*
         * Absence.avec certificat
         */
        if (MercrediConstantes::ABSENCE_AVEC_CERTIF == $presence->getAbsent()) {
            return 0;
        }
        $jour = $presence->getJour();
        if ($jour->isPedagogique()) {
            if ($presence->isHalf()) {
                $prix = $jour->getPrix2();
            } else {
                $prix = $jour->getPrix1();
            }
        } else {
            $ordre = $this->ordreService->getOrdreOnPresence($presence);
            $prix = $this->getPrixByOrdre($jour, $ordre);
        }

        $cout = $this->reductionApplicate($presence, $prix);

        return $cout;
    }

    private function reductionApplicate(PresenceInterface $presence, float $cout)
    {
        if ($reduction = $presence->getReduction()) {
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
