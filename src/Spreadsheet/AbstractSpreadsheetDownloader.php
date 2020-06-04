<?php

namespace AcMarche\Mercredi\Spreadsheet;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

abstract class AbstractSpreadsheetDownloader
{
    public function downloadXls(Spreadsheet $phpExcelObject, string $fileName): Response
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
