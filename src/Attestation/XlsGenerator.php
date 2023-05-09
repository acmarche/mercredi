<?php

namespace AcMarche\Mercredi\Attestation;

use AcMarche\Mercredi\Contrat\Attestation\AttestationGeneratorInterface;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Utils\StringUtils;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class XlsGenerator
{
    private Worksheet $worksheet;

    public function __construct(
        private AttestationGeneratorInterface $attestationGenerator
    ) {
    }

    public function forSpf(int $year): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $this->worksheet = $spreadsheet->getActiveSheet();

        $this->setTitles();
        $ligne = 2;
        $data = $this->attestationGenerator->getDataByYear($year);
        foreach ($data as $item) {
            $enfant = $item['enfant'];

            foreach ($item['tuteurs'] as $row) {
                $tuteur = $row['tuteur'];
                $presences = $row['presences'];
                $total = $row['total'];
                $this->addLine($enfant, $tuteur, $presences, $total, $ligne, $year);
                $ligne++;
            }
        }

        return $spreadsheet;
    }

    private function addLine(
        Enfant $enfant,
        Tuteur $tuteur,
        array $presences,
        float $total,
        int $ligne,
        int $year
    ): void {
        $lettre = 'A';
        $this->worksheet
            ->setCellValue($lettre++.$ligne, 'eft-'.$enfant->getId().'-tut-'.$tuteur->getId())
            ->setCellValue($lettre++.$ligne, StringUtils::cleanNationalRegister($enfant->getRegistreNational(), true))
            ->setCellValue($lettre++.$ligne, $enfant->getNom())
            ->setCellValue($lettre++.$ligne, $enfant->getPrenom())
            ->setCellValue($lettre++.$ligne, $enfant->getBirthday()?->format('d/m/Y'))
            ->setCellValue($lettre++.$ligne, $tuteur->getRue())
            ->setCellValue($lettre++.$ligne, $tuteur->getCodePostal())
            ->setCellValue($lettre++.$ligne, $tuteur->getLocalite())
            ->setCellValue($lettre++.$ligne, 150)
            ->setCellValue($lettre++.$ligne, StringUtils::cleanNationalRegister($tuteur->getRegistreNational(), true))
            ->setCellValue($lettre++.$ligne, $tuteur->getNom())
            ->setCellValue($lettre++.$ligne, $tuteur->getPrenom())
            ->setCellValue($lettre++.$ligne, ' ')
            ->setCellValue($lettre++.$ligne, $tuteur->getRue())
            ->setCellValue($lettre++.$ligne, $tuteur->getCodePostal())
            ->setCellValue($lettre++.$ligne, $tuteur->getLocalite())
            ->setCellValue($lettre++.$ligne, 150)
            ->setCellValue($lettre++.$ligne, '01/01/'.$year)
            ->setCellValue($lettre++.$ligne, '31/12/'.$year)
            ->setCellValue($lettre++.$ligne, count($presences))
            ->setCellValue($lettre++.$ligne, '')
            ->setCellValue($lettre++.$ligne, $total);
    }

    private function setTitles(): void
    {
        $lettre = 'A';
        $ligne = 1;

        $this->worksheet
            ->setCellValue($lettre++.$ligne, 'Votre référence')
            ->setCellValue($lettre++.$ligne, 'ENFANT Numéro national')
            ->setCellValue($lettre++.$ligne, 'ENFANT Nom')
            ->setCellValue($lettre++.$ligne, 'ENFANT Prénom')
            ->setCellValue($lettre++.$ligne, 'ENFANT Date de naissance (DD/MM/JJJ)')
            ->setCellValue($lettre++.$ligne, 'ENFANT Adresse')
            ->setCellValue($lettre++.$ligne, 'ENFANT Code postal')
            ->setCellValue($lettre++.$ligne, 'ENFANT Commune/Ville')
            ->setCellValue($lettre++.$ligne, 'ENFANT Code Pays')
            ->setCellValue($lettre++.$ligne, 'DÉBITEUR Numéro national')
            ->setCellValue($lettre++.$ligne, 'DÉBITEUR Nom')
            ->setCellValue($lettre++.$ligne, 'DÉBITEUR Prénom')
            ->setCellValue($lettre++.$ligne, 'DÉBITEUR Laisser vide')
            ->setCellValue($lettre++.$ligne, 'DÉBITEUR Adresse')
            ->setCellValue($lettre++.$ligne, 'Code postal')
            ->setCellValue($lettre++.$ligne, 'DÉBITEUR Commune/Ville')
            ->setCellValue($lettre++.$ligne, 'DÉBITEUR Code Pays')
            ->setCellValue($lettre++.$ligne, 'PÉRIODE 1 Date début (J/MM/AAAA)')
            ->setCellValue($lettre++.$ligne, 'PÉRIODE 1 Date fin (J/MM/AAAA)')
            ->setCellValue($lettre++.$ligne, 'PÉRIODE 1 Nombre de jours')
            ->setCellValue($lettre++.$ligne, 'PÉRIODE 1 Tarif journalier')
            ->setCellValue($lettre++.$ligne, 'PÉRIODE 1 Montant')
            ->setCellValue($lettre++.$ligne, 'PÉRIODE 2 Date début (J/MM/AAAA)')
            ->setCellValue($lettre++.$ligne, 'PÉRIODE 2 Date fin (J/MM/AAAA)')
            ->setCellValue($lettre++.$ligne, 'PÉRIODE 2 Nombre de jours')
            ->setCellValue($lettre++.$ligne, 'PÉRIODE 2 Tarif journalier')
            ->setCellValue($lettre++.$ligne, 'PÉRIODE 2 Montant')
            ->setCellValue($lettre++.$ligne, 'PÉRIODE 3 Date début (J/MM/AAAA)')
            ->setCellValue($lettre++.$ligne, 'PÉRIODE 3 Date fin (J/MM/AAAA)')
            ->setCellValue($lettre++.$ligne, 'PÉRIODE 3 Nombre de jours')
            ->setCellValue($lettre++.$ligne, 'PÉRIODE 3 Tarif journalier')
            ->setCellValue($lettre++.$ligne, 'PÉRIODE 4 Date début (J/MM/AAAA)')
            ->setCellValue($lettre++.$ligne, 'PÉRIODE 4 Date fin (J/MM/AAAA)')
            ->setCellValue($lettre++.$ligne, 'PÉRIODE 4 Nombre de jours')
            ->setCellValue($lettre++.$ligne, 'PÉRIODE 4 Tarif journalier')
            ->setCellValue($lettre++.$ligne, 'PÉRIODE 4 Montant');
    }
}

