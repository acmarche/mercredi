<?php

namespace AcMarche\Mercredi\Facture\Calculator;

use AcMarche\Mercredi\Facture\Dto\FactureDetailDto;
use AcMarche\Mercredi\Facture\FactureInterface;
use AcMarche\Mercredi\Facture\Repository\FactureComplementRepository;
use AcMarche\Mercredi\Facture\Repository\FactureDecompteRepository;
use AcMarche\Mercredi\Facture\Repository\FacturePresenceRepository;
use AcMarche\Mercredi\Facture\Repository\FactureReductionRepository;
use AcMarche\Mercredi\Reduction\Calculator\ReductionCalculator;

class FactureCalculator implements FactureCalculatorInterface
{
    private FacturePresenceRepository $facturePresenceRepository;
    private FactureReductionRepository $factureReductionRepository;
    private FactureComplementRepository $factureComplementRepository;
    private ReductionCalculator $reductionCalculator;
    private FactureDecompteRepository $factureDecompteRepository;

    public function __construct(
        FacturePresenceRepository $facturePresenceRepository,
        FactureReductionRepository $factureReductionRepository,
        FactureComplementRepository $factureComplementRepository,
        FactureDecompteRepository $factureDecompteRepository,
        ReductionCalculator $reductionCalculator
    ) {
        $this->facturePresenceRepository = $facturePresenceRepository;
        $this->factureReductionRepository = $factureReductionRepository;
        $this->factureComplementRepository = $factureComplementRepository;
        $this->reductionCalculator = $reductionCalculator;
        $this->factureDecompteRepository = $factureDecompteRepository;
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
        $factureDetail->totalReductionForfaits = $this->totalReductionForfaits($facture);
        $factureDetail->totalReductionPourcentage = $this->totalReductionPourcentage($facture);
        $factureDetail->totalComplementForfaits = $this->totalComplementForfaits($facture);
        $factureDetail->totalComplementPourcentage = $this->totalComplementPourcentage($facture);
        $factureDetail->totalDecomptes = $this->totalDecomptes($facture);

        $factureDetail->total = $factureDetail->totalPresences + $factureDetail->totalAccueils + $factureDetail->totalComplementForfaits;
        $factureDetail->total = $factureDetail->total - $factureDetail->totalReductionForfaits;
        $factureDetail->totalHorsPourcentage = $factureDetail->total;

        $factureDetail->pourcentageEnPlus = $this->reductionCalculator->calculatePourcentage(
            $factureDetail->totalComplementPourcentage,
            $factureDetail->totalHorsPourcentage
        );

        $factureDetail->pourcentageEnMoins = $this->reductionCalculator->calculatePourcentage(
            $factureDetail->totalReductionPourcentage,
            $factureDetail->totalHorsPourcentage
        );

        $factureDetail->total += $factureDetail->pourcentageEnPlus;
        $factureDetail->total -= $factureDetail->pourcentageEnMoins;

        return $factureDetail;
    }

    public function totalPresences(FactureInterface $facture): float
    {
        $facturePresences = $this->facturePresenceRepository->findByFactureAndType(
            $facture,
            FactureInterface::OBJECT_PRESENCE
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
            FactureInterface::OBJECT_ACCUEIL
        );

        $cout = 0;

        foreach ($factureAccueils as $factureAccueil) {
            $cout += $factureAccueil->getCoutCalculated();
        }

        return $cout;
    }

    public function totalReductionForfaits(FactureInterface $facture): float
    {
        $reductionForfait = 0;
        foreach ($this->factureReductionRepository->findByFacture($facture) as $reduction) {
            if ($reduction->getForfait() > 0) {
                $reductionForfait += $reduction->getForfait();
            }
        }

        return $reductionForfait;
    }

    public function totalDecomptes(FactureInterface $facture): float
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

    public function totalComplementForfaits(FactureInterface $facture): float
    {
        $complementForfait = 0;
        foreach ($this->factureComplementRepository->findByFacture($facture) as $complement) {
            if ($complement->getForfait() > 0) {
                $complementForfait += $complement->getForfait();
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
}
