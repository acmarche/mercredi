<?php


namespace AcMarche\Mercredi\Presence\Spreadsheet;


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class GeneratorXls
{
    public function __construct()
    {
    }

    public function generatePresence(): Spreadsheet
    {
        $membres = $this->membreRepository->findAll();

        $phpExcelObject = new Spreadsheet();
        $active = $phpExcelObject->setActiveSheetIndex(0);

        $c = 1;
        $lettre = 'A';
        $active
            ->setCellValue($lettre++.$c, 'Id')
            ->setCellValue($lettre++.$c, 'Civilite')
            ->setCellValue($lettre++.$c, 'Nom')
            ->setCellValue($lettre++.$c, 'Prénom')
            ->setCellValue($lettre++.$c, 'Rue')
            ->setCellValue($lettre++.$c, 'Numéro')
            ->setCellValue($lettre++.$c, 'Code postal')
            ->setCellValue($lettre++.$c, 'Localité')
            ->setCellValue($lettre++.$c, 'Telephone')
            ->setCellValue($lettre++.$c, 'Email')
            ->setCellValue($lettre++.$c, 'Created');

        $l = 2;

        foreach ($membres as $membre) {
            $lettre = 'A';
            $active
                ->setCellValue($lettre++.$l, $membre->getId())
                ->setCellValue($lettre++.$l, $membre->getCivilite())
                ->setCellValue($lettre++.$l, $membre->getNom())
                ->setCellValue($lettre++.$l, $membre->getPrenom())
                ->setCellValue($lettre++.$l, $membre->getRue())
                ->setCellValue($lettre++.$l, $membre->getNumero())
                ->setCellValue($lettre++.$l, $membre->getCodepostal())
                ->setCellValue($lettre++.$l, $membre->getLocalite())
                ->setCellValue($lettre++.$l, $membre->getTelephone())
                ->setCellValue($lettre++.$l, $membre->getEmail());
            ++$l;
        }

        $phpExcelObject->getActiveSheet()->setTitle('Membres');
        $phpExcelObject->setActiveSheetIndex(0);

        return $phpExcelObject;
    }

    private function downloadXls(Spreadsheet $phpExcelObject, string $fileName): Response
    {
        $writer = new Xlsx($phpExcelObject);
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        // Create the excel file in the tmp directory of the system
        try {
            $writer->save($temp_file);
        } catch (Exception $e) {
        }

        $response = new BinaryFileResponse($temp_file);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE,
            null === $fileName ? $response->getFile()->getFilename() : $fileName
        );

        return $response;
    }
}
