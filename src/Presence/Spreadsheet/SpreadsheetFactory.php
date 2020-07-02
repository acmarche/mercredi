<?php

namespace AcMarche\Mercredi\Presence\Spreadsheet;

use AcMarche\Mercredi\Entity\Presence;
use AcMarche\Mercredi\Presence\Dto\ListingPresenceByMonth;
use AcMarche\Mercredi\Presence\Utils\PresenceUtils;
use AcMarche\Mercredi\Scolaire\Utils\ScolaireUtils;
use AcMarche\Mercredi\Spreadsheet\AbstractSpreadsheetDownloader;
use AcMarche\Mercredi\Utils\DateUtils;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class SpreadsheetFactory extends AbstractSpreadsheetDownloader
{
    /**
     * @var ScolaireUtils
     */
    private $scolaireUtils;

    public function __construct(ScolaireUtils $scolaireUtils)
    {
        $this->scolaireUtils = $scolaireUtils;
    }

    public function createXlsByMonthForOne(\DateTime $date, ListingPresenceByMonth $listingPresenceByMonth): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $dateInterval = DateUtils::getDatePeriod($date);

        /**
         * titre de la feuille.
         */
        $ligne = 1;
        $sheet
            ->setCellValue('A'.$ligne, 'Nom')
            ->setCellValue('B'.$ligne, 'Prénom')
            ->setCellValue('C'.$ligne, 'Né le')
            ->setCellValue('D'.$ligne, 'Groupe');

        $colonne = 'E';

        foreach ($dateInterval as $date) {
            $sheet->setCellValue($colonne.$ligne, DateUtils::formatFr($date, \IntlDateFormatter::SHORT));
            ++$colonne;
        }

        $ligne = 3;
        foreach ($listingPresenceByMonth->getEnfants() as $enfant) {
            $colonne = 'A';

            $neLe = $enfant->getBirthday() ? $enfant->getBirthday()->format('d-m-Y') : '';
            $groupe = $this->scolaireUtils->findGroupeScolaireEnfantByAnneeScolaire($enfant);

            $sheet
                ->setCellValue($colonne.$ligne, $enfant->getNom())
                ->setCellValue(++$colonne.$ligne, $enfant->getPrenom())
                ->setCellValue(++$colonne.$ligne, $neLe)
                ->setCellValue(++$colonne.$ligne, $groupe->getNom());

            foreach ($dateInterval as $date) {
                //$presence = $this->plaineService->getPresenceByDateAndEnfant($date, $enfant);

                /*  if (!$presence) {
                      ++$colonne;
                      continue;
                  }*/

                ++$colonne;
                $sheet->setCellValue($colonne.$ligne, 1);
            }
            ++$ligne;
        }

        return $spreadsheet;
    }

    public function createXlsByMonthDefault(ListingPresenceByMonth $listingPresenceByMonth): Spreadsheet
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
         * Pour chaque enfant on affiche present ou pas et son total.
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
                    ++$countPresences;
                }
                $sheet->setCellValue(++$colonne.$ligne, $present);
            }
            $sheet->setCellValue(++$colonne.$ligne, $countPresences);
            ++$ligne;
        }

        /**
         * Total enfants et total enfants par jour.
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

    /**
     * @param Presence[] $presences
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function presenceXls(array $presences): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $c = 1;
        $sheet
            ->setCellValue('A'.$c, 'Nom')
            ->setCellValue('B'.$c, 'Prénom')
            ->setCellValue('C'.$c, 'Né le');

        $ligne = 3;

        $enfants = PresenceUtils::extractEnfants($presences);

        foreach ($enfants as $enfant) {
            $colonne = 'A';
            $neLe = $enfant->getBirthday() ? $enfant->getBirthday()->format('d-m-Y') : '';
            $sheet
                ->setCellValue($colonne.$ligne, $enfant->getNom())
                ->setCellValue(++$colonne.$ligne, $enfant->getPrenom())
                ->setCellValue(++$colonne.$ligne, $neLe);
            ++$ligne;
        }

        return $spreadsheet;
    }
}
