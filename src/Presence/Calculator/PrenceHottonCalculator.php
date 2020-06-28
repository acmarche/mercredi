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
            }
            $prix = $jour->getPrix1();
        } else {
            $ordre = $this->getOrder($presence);
            $prix = $this->getPrixByOrdre($jour, $ordre);
        }

        $cout = $this->reductionApplicate($presence, $prix);

        return $cout;
    }

    /**
     * @throws \Exception
     */
    private function getOrder(PresenceInterface $presence): float
    {
        //on a forcé l'ordre
        if ($ordre = $presence->getOrdre()) {
            return $ordre;
        }

        $tuteur = $presence->getTuteur();
        $enfant = $presence->getEnfant();
        $ordreBase = $this->ordreService->getOrdreEnfant($enfant, $tuteur);
        //quand enfant premier, fraterie pas d'importance
        if (1 == $ordreBase) {
            return $ordreBase;
        }

        /**
         * Ordre suivant la fratries.
         */
        $fratries = $this->relationRepository->findFrateries(
            $enfant,
            [$tuteur]
        );

        if (0 == count($fratries)) {
            return $ordreBase;
        }

        $jour = $presence->getJour();

        $presents = [];
        foreach ($fratries as $fratry) {
            if ($this->presenceRepository->findByEnfantAndJour($fratry, $jour)) {
                $presents[] = $fratry;
            }
        }

        if (0 == count($presents)) {
            return $ordreBase;
        }

        return $ordreBase - count($presents);

        //lisa = 2, si marie en 1 reste 2
        //lisa = 3, si marie en 1 devient 2
        foreach ($presents as $frere) {
            $ordreFrere = $this->ordreService->getOrdreEnfant($frere, $tuteur);
        }

        //si ordre enfant = 1, peu importe
        //si ordre enfant = 2, doit avoir 1 fratrie
        //si ordre enfant = 3, doit avoir 2 fratries
        //si ordre enfant = 4, doit avoir 3 fratries
        /*  if ($ordre_fiche > 1) {
              if (($ordre_fiche - 1) == $fratries_count) {
                  //var_dump("ok");
              } else {
                  //var_dump("ko");
              }
          }*/

        return $ordreBase;
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
