<?php

namespace AcMarche\Mercredi\Facture\Calculator;

use AcMarche\Mercredi\Facture\FactureInterface;
use AcMarche\Mercredi\Facture\Repository\FactureComplementRepository;
use AcMarche\Mercredi\Facture\Repository\FacturePresenceRepository;
use AcMarche\Mercredi\Facture\Repository\FactureReductionRepository;

class FactureCalculator implements FactureCalculatorInterface
{
    private FacturePresenceRepository $facturePresenceRepository;
    private FactureReductionRepository $factureReductionRepository;
    private FactureComplementRepository $factureComplementRepository;

    public function __construct(
        FacturePresenceRepository $facturePresenceRepository,
        FactureReductionRepository $factureReductionRepository,
        FactureComplementRepository $factureComplementRepository
    ) {
        $this->facturePresenceRepository = $facturePresenceRepository;
        $this->factureReductionRepository = $factureReductionRepository;
        $this->factureComplementRepository = $factureComplementRepository;
    }

    public function coutTotal(FactureInterface $facture): float
    {
        $facturePresences = $this->facturePresenceRepository->findByFactureAndType(
            $facture,
            FactureInterface::OBJECT_PRESENCE
        );
        $factureAccueils = $this->facturePresenceRepository->findByFactureAndType(
            $facture,
            FactureInterface::OBJECT_ACCUEIL
        );

        $cout = 0;
        foreach ($facturePresences as $facturePresence) {
            $cout += $facturePresence->getCout();
        }

        foreach ($factureAccueils as $factureAccueil) {
            $cout += $factureAccueil->getCout();
        }

        $reductionPourcentage = 0;
        $reductionForfait = 0;
        foreach ($this->factureReductionRepository->findByFacture($facture) as $reduction) {
            if ($pourcentage = $reduction->getPourcentage() > 0) {
                $reductionPourcentage += $pourcentage;
            }
            if ($forfait = $reduction->getForfait() > 0) {
                $reductionForfait += $forfait;
            }
        }

        $complementPourcentage = 0;
        $complementForfait = 0;
        foreach ($this->factureComplementRepository->findByFacture($facture) as $complement) {
            if ($pourcentage = $complement->getPourcentage() > 0) {
                $complementPourcentage += $pourcentage;
            }
            if ($forfait = $complement->getForfait() > 0) {
                $complementForfait += $forfait;
            }
        }

        return $cout;
    }
}
