<?php


namespace AcMarche\Mercredi\Presence\Spreadsheet;

use AcMarche\Mercredi\Presence\Dto\ListingPresenceByMonth;
use AcMarche\Mercredi\Scolaire\ScolaireData;
use AcMarche\Mercredi\Spreadsheet\AbstractSpreadsheetDownloader;
use AcMarche\Mercredi\Utils\DateProvider;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class SpreadsheetFactory extends AbstractSpreadsheetDownloader
{
    public function createXlsOne(\DateTime $date, ListingPresenceByMonth $listingPresenceByMonth): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $dateInterval = DateProvider::getDatePeriod($date);

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

    public function createXls(ListingPresenceByMonth $listingPresenceByMonth): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        /**
         * title.
         */
        $ligne = 1;
        $colonne = 'A';
        $sheet
            ->setCellValue($colonne.$ligne, 'Nom')
            ->setCellValue(++$colonne.$ligne, 'Prénom')
            ->setCellValue(++$colonne.$ligne, 'Né le');

        foreach ($listingPresenceByMonth->getJoursListing() as $jourListing) {
            $sheet->setCellValue(++$colonne.$ligne, $jourListing->getJour()->getDateJour()->format('d-m-Y'));
        }
        $sheet->setCellValue(++$colonne.$ligne, 'Total');

        /**
         * Pour chaque enfant on affiche present ou pas et son total
         */
        $ligne = 3;
        foreach ($listingPresenceByMonth->getEnfants() as $enfant) {
            $colonne = 'A';

            $neLe = $enfant->getBirthday() ? $enfant->getBirthday()->format('d-m-Y') : '';
            $sheet
                ->setCellValue($colonne.$ligne, $enfant->getNom())
                ->setCellValue(++$colonne.$ligne, $enfant->getPrenom())
                ->setCellValue(++$colonne.$ligne, $neLe);

            $countPresences = 0;
            foreach ($listingPresenceByMonth->getJoursListing() as $jourListing) {
                $present = 0;
                if ($jourListing->hasEnfant($enfant)) {
                    $present = 1;
                    $countPresences++;
                }
                $sheet->setCellValue(++$colonne.$ligne, $present);
            }
            $sheet->setCellValue(++$colonne.$ligne, $countPresences);
            ++$ligne;
        }

        /**
         * Total enfants et total enfants par jour
         */
        $colonne = 'A';
        $sheet->setCellValue($colonne.$ligne, count($listingPresenceByMonth->getEnfants()).' enfants');
        $colonne = 'D';

        foreach ($listingPresenceByMonth->getJoursListing() as $jourListing) {
            $sheet->setCellValue($colonne.$ligne, count($jourListing->getEnfants()));
            ++$colonne;
        }

        $sheet->setCellValue($colonne.$ligne, count($listingPresenceByMonth->getPresences()));

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
