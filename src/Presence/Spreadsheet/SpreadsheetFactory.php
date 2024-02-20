<?php

namespace AcMarche\Mercredi\Presence\Spreadsheet;

use AcMarche\Mercredi\Contrat\Facture\FactureCalculatorInterface;
use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Entity\Presence\Presence;
use AcMarche\Mercredi\Presence\Dto\ListingPresenceByMonth;
use AcMarche\Mercredi\Presence\Utils\PresenceUtils;
use AcMarche\Mercredi\Scolaire\Utils\ScolaireUtils;
use AcMarche\Mercredi\Spreadsheet\SpreadsheetDownloaderTrait;
use AcMarche\Mercredi\Utils\DateUtils;
use IntlDateFormatter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

final class SpreadsheetFactory
{
    use SpreadsheetDownloaderTrait;

    /**
     * @var string
     */
    private const FORMAT = 'd-m-Y';

    public function __construct(
        private ScolaireUtils $scolaireUtils,
        private readonly FactureCalculatorInterface $factureCalculator
    ) {
    }

    public function createXlsByMonthForOne(
        \DateTime|\DateTimeImmutable $dateTime,
        ListingPresenceByMonth $listingPresenceByMonth
    ): Spreadsheet {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();

        $datePeriod = DateUtils::getDatePeriod($dateTime);

        /**
         * titre de la feuille.
         */
        $ligne = 1;
        $worksheet
            ->setCellValue('A'.$ligne, 'nom')
            ->setCellValue('B'.$ligne, 'Prénom')
            ->setCellValue('C'.$ligne, 'Né le')
            ->setCellValue('D'.$ligne, 'Groupe');

        $colonne = 'E';

        foreach ($datePeriod as $dateTime) {
            $worksheet->setCellValue($colonne.$ligne, DateUtils::formatFr($dateTime, IntlDateFormatter::SHORT));
            ++$colonne;
        }

        $ligne = 3;
        foreach ($listingPresenceByMonth->enfants as $enfant) {
            $colonne = 'A';

            $neLe = null !== $enfant->getBirthday() ? $enfant->getBirthday()->format(self::FORMAT) : '';
            $groupe = $this->scolaireUtils->findGroupeScolaireEnfantByAnneeScolaire($enfant);

            $worksheet
                ->setCellValue($colonne.$ligne, $enfant->getNom())
                ->setCellValue(++$colonne.$ligne, $enfant->getPrenom())
                ->setCellValue(++$colonne.$ligne, $neLe)
                ->setCellValue(++$colonne.$ligne, $groupe->getNom());

            foreach ($datePeriod as $dateTime) {
                ++$colonne;
                $worksheet->setCellValue($colonne.$ligne, 1);
            }
            ++$ligne;
        }

        return $spreadsheet;
    }

    public function createXlsByMonthDefault(ListingPresenceByMonth $listingPresenceByMonth): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();

        /**
         * title.
         */
        $ligne = 1;
        $colonne = 'A';
        $worksheet
            ->setCellValue($colonne.$ligne, 'nom')
            ->setCellValue(++$colonne.$ligne, 'Prénom')
            ->setCellValue(++$colonne.$ligne, 'Né le');

        foreach ($listingPresenceByMonth->joursListing as $jourListing) {
            $worksheet->setCellValue(++$colonne.$ligne, $jourListing->getJour()->getDateJour()->format(self::FORMAT));
        }
        $worksheet->setCellValue(++$colonne.$ligne, 'Total');

        /**
         * Pour chaque enfant on affiche present ou pas et son total.
         */
        $ligne = 3;
        foreach ($listingPresenceByMonth->enfants as $enfant) {
            $colonne = 'A';

            $neLe = null !== $enfant->getBirthday() ? $enfant->getBirthday()->format(self::FORMAT) : '';
            $worksheet
                ->setCellValue($colonne.$ligne, $enfant->getNom())
                ->setCellValue(++$colonne.$ligne, $enfant->getPrenom())
                ->setCellValue(++$colonne.$ligne, $neLe);

            $countPresences = 0;
            foreach ($listingPresenceByMonth->joursListing as $jourListing) {
                $present = 0;
                if ($jourListing->hasEnfant($enfant)) {
                    $present = 1;
                    ++$countPresences;
                }
                $worksheet->setCellValue(++$colonne.$ligne, $present);
            }
            $worksheet->setCellValue(++$colonne.$ligne, $countPresences);
            ++$ligne;
        }

        /**
         * Total enfants et total enfants par jour.
         */
        $colonne = 'A';
        $worksheet->setCellValue($colonne.$ligne, \count($listingPresenceByMonth->enfants).' enfants');
        $colonne = 'D';

        foreach ($listingPresenceByMonth->joursListing as $jourListing) {
            $worksheet->setCellValue($colonne.$ligne, \count($jourListing->getEnfants()));
            ++$colonne;
        }

        $worksheet->setCellValue($colonne.$ligne, \count($listingPresenceByMonth->presences));

        return $spreadsheet;
    }

    /**
     * @param Presence[] $presences
     */
    public function presenceXls(array $presences): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet
            ->setCellValue('A1', 'nom')
            ->setCellValue('B1', 'Prénom')
            ->setCellValue('C1', 'Né le');

        $ligne = 3;

        $enfants = PresenceUtils::extractEnfants($presences);

        foreach ($enfants as $enfant) {
            $colonne = 'A';
            $neLe = null !== $enfant->getBirthday() ? $enfant->getBirthday()->format(self::FORMAT) : '';
            $worksheet
                ->setCellValue($colonne.$ligne, $enfant->getNom())
                ->setCellValue(++$colonne.$ligne, $enfant->getPrenom())
                ->setCellValue(++$colonne.$ligne, $neLe);
            ++$ligne;
        }

        return $spreadsheet;
    }

    /**
     * @param Facture[] $factures
     */
    public function facturesXls(array $factures): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet
            ->setCellValue('A1', 'Numéro facture')
            ->setCellValue('B1', 'Date facture')
            ->setCellValue('C1', 'Nom parent')
            ->setCellValue('D1', 'Prénom parent')
            ->setCellValue('E1', 'Enfant(s)')
            ->setCellValue('F1', 'Montant facturé')
            ->setCellValue('G1', 'Montant payé')
            ->setCellValue('H1', 'Payé le')
            ->setCellValue('I1', 'Communication');

        $ligne = 2;

        foreach ($factures as $facture) {
            $colonne = 'A';
            $factureDetailDto = $this->factureCalculator->createDetail($facture);
            if ($facture->getPayeLe()) {
                $montantPaye = $factureDetailDto->total;
                $payeLe = $facture->getPayeLe()->format(self::FORMAT);
            } else {
                $montantPaye = $factureDetailDto->totalDecomptes;
                $payeLe = '';
            }

            $worksheet
                ->setCellValue($colonne.$ligne, $facture->getId())
                ->setCellValue(++$colonne.$ligne, $facture->getFactureLe()->format(self::FORMAT))
                ->setCellValue(++$colonne.$ligne, $facture->getNom())
                ->setCellValue(++$colonne.$ligne, $facture->getPrenom())
                ->setCellValue(++$colonne.$ligne, join(',', $facture->getEnfants()))
                ->setCellValue(++$colonne.$ligne, $factureDetailDto->total)
                ->setCellValue(++$colonne.$ligne, $montantPaye)
                ->setCellValue(++$colonne.$ligne, $payeLe)
                ->setCellValue(++$colonne.$ligne, $facture->getCommunication());

            ++$ligne;
        }

        return $spreadsheet;
    }
}
