<?php

namespace AcMarche\Mercredi\Attestation;

use AcMarche\Mercredi\Contrat\Facture\FactureCalculatorInterface;
use AcMarche\Mercredi\Contrat\Presence\PresenceCalculatorInterface;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;

class AttestationGenerator
{
    public function __construct(
        private PresenceRepository $presenceRepository,
        private PresenceCalculatorInterface $presenceCalculator,
        private FactureCalculatorInterface $factureCalculator,
    ) {
    }

    public function newOne(array $presences): array
    {
        return $this->treatment($presences);
    }

    public function getDataByYear(int $year): array
    {
        $presences = $this->presenceRepository->findByYear($year);

        return $this->treatment($presences);

    }

    private function treatment(array $presences): array
    {
        $presencesPaid = [];
        foreach ($presences as $presence) {
            if ($this->factureCalculator->isPresencePaid($presence)) {
                $presence->cout = $this->presenceCalculator->calculate($presence);
                $presencesPaid[] = $presence;
            }
        }

        $data = [];
        foreach ($presencesPaid as $presence) {
            $enfant = $presence->getEnfant();
            $idEnfant = $enfant->getId();
            $tuteur = $presence->getTuteur();
            $idTuteur = $tuteur->getId();

            $data[$idEnfant]['enfant'] = $enfant;
            $data[$idEnfant]['tuteurs'][$idTuteur]['tuteur'] = $tuteur;
            $data[$idEnfant]['tuteurs'][$idTuteur]['presences'][] = $presence;
            if (!isset($data[$idEnfant]['tuteurs'][$idTuteur]['total'])) {
                $data[$idEnfant]['tuteurs'][$idTuteur]['total'] = 0;
            }
            $data[$idEnfant]['tuteurs'][$idTuteur]['total'] += $presence->cout;
        }

        return $data;
    }
}