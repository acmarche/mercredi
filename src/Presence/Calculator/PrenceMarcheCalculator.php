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
    public array $ecoles;
    private OrdreService $ordreService;
    private ReductionCalculator $reductionCalculator;

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
        if (null !== $jour->getPlaine()) {
            return $this->calculatePlaine($presence, $jour);
        }

        return $this->calculatePresence($presence);
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

        switch ($ordre) {
            case 2:
                $prix = $plaine->getPrix2();
                break;
            case 3:
                $prix = $plaine->getPrix3();
                break;
            default:
                $prix = $plaine->getPrix1();
                break;
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

    public function setMetaDatas(PresenceInterface $presence, FacturePresence $facturePresence): void
    {
        $facturePresence->setPedagogique($presence->getJour()->isPedagogique());
        $facturePresence->setPresenceDate($presence->getJour()->getDateJour());
        $enfant = $presence->getEnfant();
        if (null !== $enfant->getEcole()) {
            $this->ecoles[] = $enfant->getEcole()->getNom();
        }
        $facturePresence->setNom($enfant->getNom());
        $facturePresence->setPrenom($enfant->getPrenom());
        $ordre = $this->getOrdreOnPresence($presence);
        $facturePresence->setOrdre($ordre);
        $facturePresence->setAbsent($presence->getAbsent());
        $facturePresence->setCoutBrut($this->getPrixByOrdre($presence, $ordre));
    }

    public function getOrdreOnPresence(PresenceInterface $presence): int
    {
        return $this->ordreService->getOrdreOnPresence($presence);
    }

    public function getPrixByOrdre(PresenceInterface $presence, int $ordre): float
    {
        $jour = $presence->getJour();
        if ($ordre >= 3) {
            return $jour->getPrix3();
        }
        if (2 == $ordre) {
            return $jour->getPrix2();
        }

        return $jour->getPrix1();
    }
}
