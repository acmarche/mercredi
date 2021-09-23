<?php

namespace AcMarche\Mercredi\Presence\Calculator;

use AcMarche\Mercredi\Data\MercrediConstantes;
use AcMarche\Mercredi\Entity\Facture\FacturePresence;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Presence\Entity\PresenceInterface;
use AcMarche\Mercredi\Reduction\Calculator\ReductionCalculator;
use AcMarche\Mercredi\Relation\Utils\OrdreService;

final class PrenceHottonCalculator implements PresenceCalculatorInterface
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

    public function setMetaDatas(PresenceInterface $presence, FacturePresence $facturePresence): void
    {
        $facturePresence->setPedagogique($presence->getJour()->isPedagogique());
        $facturePresence->setPresenceDate($presence->getJour()->getDateJour());
        $enfant = $presence->getEnfant();
        if ($enfant->getEcole()) {
            $this->ecoles[] = $enfant->getEcole()->getNom();
        }
        $facturePresence->setNom($enfant->getNom());
        $facturePresence->setPrenom($enfant->getPrenom());
        $ordre = $this->getOrdreOnPresence($presence);
        $facturePresence->setOrdre($ordre);
        $facturePresence->setAbsent($presence->getAbsent());
        $facturePresence->setCoutBrut($this->getPrixByOrdre($presence, $ordre));
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

        if (null !== $jour->getPlaine()) {
            return $this->calculatePlaine($presence, $jour);
        }

        return $this->calculatePresence($presence, $jour);
    }

    public function getPrixByOrdre(PresenceInterface $presence, $ordre): float
    {
        $jour = $presence->getJour();

        if ($jour->isPedagogique()) {
            return $presence->isHalf() ? $jour->getPrix2() : $jour->getPrix1();
        }

        switch ($ordre) {
            case 2:
                return $jour->getPrix2();
            case 3:
                return $jour->getPrix3();
            default:
                return $jour->getPrix1();
        }
    }

    public function getOrdreOnPresence(PresenceInterface $presence): int
    {
        return $this->ordreService->getOrdreOnPresence($presence);
    }

    private function calculatePresence(PresenceInterface $presence, Jour $jour): float
    {
        $ordre = $this->getOrdreOnPresence($presence);
        $prix = $this->getPrixByOrdre($presence, $ordre);

        return $this->reductionApplicate($presence, $prix);
    }

    private function calculatePlaine(PresenceInterface $presence, Jour $jour): float
    {
        $plaine = $jour->getPlaine();
        $ordre = $this->getOrdreOnPresence($presence);
        $prix = $plaine->getPrix1();
        //todo !!!! prix plaine

        if ($ordre > 1) {
            $prix = $plaine->getPrix1();
        }

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
