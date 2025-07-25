<?php

namespace AcMarche\Mercredi\Facture\Calculator;

use AcMarche\Mercredi\Contrat\Facture\FactureCalculatorInterface;
use AcMarche\Mercredi\Entity\Presence\Accueil;
use AcMarche\Mercredi\Entity\Presence\Presence;
use AcMarche\Mercredi\Facture\Dto\FactureDetailDto;
use AcMarche\Mercredi\Facture\FactureInterface;
use AcMarche\Mercredi\Facture\Repository\FactureComplementRepository;
use AcMarche\Mercredi\Facture\Repository\FactureDecompteRepository;
use AcMarche\Mercredi\Facture\Repository\FacturePresenceRepository;
use AcMarche\Mercredi\Facture\Repository\FactureReductionRepository;
use AcMarche\Mercredi\Reduction\Calculator\ReductionCalculator;

class FactureCalculator implements FactureCalculatorInterface
{
    public function __construct(
        private FacturePresenceRepository $facturePresenceRepository,
        private FactureReductionRepository $factureReductionRepository,
        private FactureComplementRepository $factureComplementRepository,
        private FactureDecompteRepository $factureDecompteRepository,
        private ReductionCalculator $reductionCalculator,
    ) {
    }

    public function total(FactureInterface $facture): float
    {
        $factureDetail = $this->createDetail($facture);

        return $factureDetail->total;
    }

    public function createDetail(FactureInterface $facture): FactureDetailDto
    {
        $factureDetail = new FactureDetailDto();
        $factureDetail->totalPresences = $this->totalPresences($facture);
        $factureDetail->totalAccueils = $this->totalAccueils($facture);
        $factureDetail->totalPlaines = $this->totalPlaine($facture);
        $factureDetail->totalReductionAmounts = $this->totalReductionAmounts($facture);
        $factureDetail->totalReductionPourcentage = $this->totalReductionPourcentage($facture);
        $factureDetail->totalComplementAmounts = $this->totalComplementAmounts($facture);
        $factureDetail->totalComplementPourcentage = $this->totalComplementPourcentage($facture);
        $factureDetail->totalDecomptes = $this->totalDecomptes($facture);

        $factureDetail->total = $factureDetail->totalPresences + $factureDetail->totalAccueils + $factureDetail->totalPlaines + $factureDetail->totalComplementAmounts;
        $factureDetail->total -= $factureDetail->totalReductionAmounts;
        $factureDetail->total -= $factureDetail->totalDecomptes;
        $factureDetail->totalHorsPourcentage = $factureDetail->total;

        $factureDetail->pourcentageEnPlus = $this->reductionCalculator->calculatePourcentage(
            $factureDetail->totalComplementPourcentage,
            $factureDetail->totalHorsPourcentage,
        );

        $factureDetail->pourcentageEnMoins = $this->reductionCalculator->calculatePourcentage(
            $factureDetail->totalReductionPourcentage,
            $factureDetail->totalHorsPourcentage,
        );

        $factureDetail->total += $factureDetail->pourcentageEnPlus;
        $factureDetail->total -= $factureDetail->pourcentageEnMoins;

        return $factureDetail;
    }

    public function totalPresences(FactureInterface $facture): float
    {
        $facturePresences = $this->facturePresenceRepository->findByFactureAndType(
            $facture,
            FactureInterface::OBJECT_PRESENCE,
        );

        $cout = 0;
        foreach ($facturePresences as $facturePresence) {
            $cout += $facturePresence->getCoutCalculated();
        }

        return $cout;
    }

    public function totalAccueils(FactureInterface $facture): float
    {
        $factureAccueils = $this->facturePresenceRepository->findByFactureAndType(
            $facture,
            FactureInterface::OBJECT_ACCUEIL,
        );

        $cout = 0;

        foreach ($factureAccueils as $factureAccueil) {
            $cout += $factureAccueil->getCoutCalculated();
        }

        return $cout;
    }

    public function totalReductionAmounts(FactureInterface $facture): float
    {
        $reductionForfait = 0;
        foreach ($this->factureReductionRepository->findByFacture($facture) as $reduction) {
            if ($reduction->getAmount() > 0) {
                $reductionForfait += $reduction->getAmount();
            }
        }

        return $reductionForfait;
    }

    public function totalDecomptes(FactureInterface $facture): float|int
    {
        $total = 0;
        foreach ($this->factureDecompteRepository->findByFacture($facture) as $decompte) {
            $total += $decompte->getMontant();
        }

        return $total;
    }

    public function totalReductionPourcentage(FactureInterface $facture): float
    {
        $reductionPourcentage = 0;
        foreach ($this->factureReductionRepository->findByFacture($facture) as $reduction) {
            if ($reduction->getPourcentage() > 0) {
                $reductionPourcentage += $reduction->getPourcentage();
            }
        }

        return $reductionPourcentage;
    }

    public function totalComplementAmounts(FactureInterface $facture): float
    {
        $complementForfait = 0;
        foreach ($this->factureComplementRepository->findByFacture($facture) as $complement) {
            if ($complement->getAmount() > 0) {
                $complementForfait += $complement->getAmount();
            }
        }

        return $complementForfait;
    }

    public function totalComplementPourcentage(FactureInterface $facture): float
    {
        $complementPourcentage = 0;
        foreach ($this->factureComplementRepository->findByFacture($facture) as $complement) {
            if ($complement->getPourcentage() > 0) {
                $complementPourcentage += $complement->getPourcentage();
            }
        }

        return $complementPourcentage;
    }

    public function totalPlaine(FactureInterface $facture): float|int
    {
        $facturePresences = $this->facturePresenceRepository->findByFactureAndType(
            $facture,
            FactureInterface::OBJECT_PLAINE,
        );

        $cout = 0;
        foreach ($facturePresences as $facturePresence) {
            $cout += $facturePresence->getCoutCalculated();
        }

        return $cout;
    }

    public function isPresencePaid(Presence $presence): bool
    {
        if ($presence->getPaiement()) {
            return true;
        }
        $presenceFacture = $this->facturePresenceRepository->findByPresence($presence);
        if ($presenceFacture) {
            $facture = $presenceFacture->getFacture();
            if ($facture->getPayeLe()) {
                return true;
            }
        }

        return false;
    }

    public function isAccueilPaid(Accueil $accueil): bool
    {
        $presenceFacture = $this->facturePresenceRepository->findByAccueil($accueil);
        if ($presenceFacture) {
            $facture = $presenceFacture->getFacture();
            if ($facture->getPayeLe()) {
                return true;
            }
        }

        return false;
    }
}
