<?php


namespace AcMarche\Mercredi\Presence\Spreadsheet;

use AcMarche\Mercredi\Presence\Dto\ListingPresenceByMonth;
use AcMarche\Mercredi\Scolaire\ScolaireData;
use AcMarche\Mercredi\Spreadsheet\AbstractSpreadsheetDownloader;
use AcMarche\Mercredi\Utils\DateProvider;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class SpreadsheetFactory extends AbstractSpreadsheetDownloader
{
    public function createXSLOne($mois, ListingPresenceByMonth $listingPresenceByMonth): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $dateInterval = DateProvider::getDateIntervale('01/'.$mois);

        /**
         * titre de la feuille.
         */
        $c = 1;
        $sheet
            ->setCellValue('A'.$c, 'Nom')
            ->setCellValue('B'.$c, 'Prénom')
            ->setCellValue('C'.$c, 'Né le')
            ->setCellValue('D'.$c, 'Groupe');

        $colonne = 'E';
        foreach ($dateInterval as $date) {
            $sheet->setCellValue($colonne.$c, $date->format('D j'));
            ++$colonne;
        }

        $ligne = 3;
        foreach ($listingPresenceByMonth->getEnfants() as $enfant) {
            $colonne = 'A';
        }
        $neLe = $enfant->getBirthday() ? $enfant->getBirthday()->format('d-m-Y') : '';
        $sheet->setCellValue($colonne.$ligne, $enfant->getNom());
        ++$colonne;
        $sheet->setCellValue($colonne.$ligne, $enfant->getPrenom());
        ++$colonne;
        $sheet->setCellValue($colonne.$ligne, $neLe);
        ++$colonne;
        $sheet->setCellValue($colonne.$ligne, ScolaireData::getGroupeScolaire($enfant));

        foreach ($dateInterval as $date) {
            $presence = $this->plaineService->getPresenceByDateAndEnfant($date, $enfant);

            if (!$presence) {
                ++$colonne;
                continue;
            }

            ++$colonne;
            $sheet->setCellValue($colonne.$ligne, 1);
        }
        ++$ligne;

        return $spreadsheet;
    }

    public function createXSLObject(ListingPresenceByMonth $listingPresenceByMonth): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $alphabets = range('A', 'Z');
        //  $lastLettre = $alphabets[(count($dates) * 2) - 1];
        /**
         * title.
         */
        $c = 1;
        $colonne = 'C';
        $sheet->setCellValue('A'.$c, 'Enfant')
            ->setCellValue('B'.$c, 'Né le');
        foreach ($listingPresenceByMonth->getPresences() as $date => $count) {
            $sheet->setCellValue($colonne.$c, $date);
            ++$colonne;
        }
        $sheet->setCellValue($colonne.$c, 'Total');

        $ligne = 3;
        foreach ($listingPresenceByMonth->getEnfants() as $enfant) {
            $colonne = 'A';
            $count = 0;
            $enfantNom = $enfant->__toString();
            $neLe = $enfant->getBirthday() ? $enfant->getBirthday()->format('d-m-Y') : '';
            $sheet->setCellValue($colonne.$ligne, $enfantNom);
            ++$colonne;
            $sheet->setCellValue($colonne.$ligne, $neLe);
            foreach ($listingPresenceByMonth->getPresences() as $date => $data) {
                $enfantsByDate = $data['enfants'];
                $txt = 0;
                if (in_array($enfant, $enfantsByDate)) {
                    $txt = '1';
                    ++$count;
                }
                ++$colonne;
                $sheet->setCellValue($colonne.$ligne, $txt);
            }
            ++$colonne;
            $sheet->setCellValue($colonne.$ligne, $count);
            ++$ligne;
        }
        $colonne = 'A';
        $sheet->setCellValue($colonne.$ligne, count($listingPresenceByMonth->getEnfants()).' enfants');
        $colonne = 'C';
        $totalmois = 0;
        foreach ($listingPresenceByMonth->getPresences() as $date => $data) {
            $sheet->setCellValue($colonne.$ligne, $data['count']);
            $totalmois += $data['count'];
            ++$colonne;
        }

        $sheet->setCellValue($colonne.$ligne, $totalmois);

        return $spreadsheet;
    }

    public function presenceXls(ListingPresenceByMonth $listingPresenceByMonth): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $c = 1;
        $sheet
            ->setCellValue('A'.$c, 'Nom')
            ->setCellValue('B'.$c, 'Prénom')
            ->setCellValue('C'.$c, 'Né le');

        $ligne = 3;
        foreach ($listingPresenceByMonth->getEnfants() as $enfant) {
            $colonne = 'A';
            $neLe = $enfant->getBirthday() ? $enfant->getBirthday()->format('d-m-Y') : '';
            $sheet->setCellValue($colonne.$ligne, $enfant->getNom());
            ++$colonne;
            $sheet->setCellValue($colonne.$ligne, $enfant->getPrenom());
            ++$colonne;
            $sheet->setCellValue($colonne.$ligne, $neLe);
            ++$ligne;
        }

        return $spreadsheet;
    }
}
