<?php

namespace AcMarche\Mercredi\Attestation;

use AcMarche\Mercredi\Contrat\Facture\FactureCalculatorInterface;
use AcMarche\Mercredi\Contrat\Presence\PresenceCalculatorInterface;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Presence\Utils\PresenceUtils;
use AcMarche\Mercredi\Utils\DateUtils;

class AttestationGenerator
{
    public function __construct(
        private PresenceRepository $presenceRepository,
        private PresenceCalculatorInterface $presenceCalculator,
        private FactureCalculatorInterface $factureCalculator,
    ) {
    }

    public function newOne(int $year, array $presences): array
    {
        $presencesPaid = [];
        foreach ($presences as $presence) {
            if ($this->factureCalculator->isPresencePaid($presence)) {
                $presencesPaid[] = $presence;
            }
        }

        foreach ($presencesPaid as $presence) {
            $presence->cout = $this->presenceCalculator->calculate($presence);
        }

        return $this->groupByQuarter($presencesPaid, $year);
    }

    public function getDataByYear(int $year): array
    {
        $presences = $this->presenceRepository->findByYear($year);

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
            $tuteur = $presence->getTuteur();
            $data[$enfant->getId()]['enfant'] = $enfant;
            $data[$enfant->getId()]['tuteurs'][$tuteur->getId()]['tuteur'] = $tuteur;
            $data[$enfant->getId()]['tuteurs'][$tuteur->getId()]['presences'][] = $presence;
        }

        return $data;
    }

    public function groupByQuarter(array $presences, int $year): array
    {
        $quarters = PresenceUtils::groupByQuarter($presences);
        $dates = DateUtils::quarterDates($year);

        $data = [
            1 => ['total' => 0, 'presences' => []],
            2 => ['total' => 0, 'presences' => []],
            3 => ['total' => 0, 'presences' => []],
            4 => ['total' => 0, 'presences' => []],
        ];

        foreach ($quarters as $key => $row) {
            $data[$key]['dates'] = $dates[$key];
            foreach ($row as $item) {
                $data[$key]['presences'][] = $item;
                $data[$key]['total'] += $item->cout;
            }
        }

        foreach ($data as $key => $row) {
            $countPresences = count($row['presences']);
            if ($countPresences > 0) {
                //$data[$key]['prix'] = number_format($row['total'] / $countPresences, 2, '.', '');
                $data[$key]['prix'] = '';
            } else {
                $data[$key]['prix'] = '';
            }
        }

        return $data;
    }
}