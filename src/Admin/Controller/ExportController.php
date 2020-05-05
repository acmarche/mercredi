<?php

namespace AcMarche\Mercredi\Admin\Controller;

use AcMarche\Mercredi\Presence\Spreadsheet\GeneratorXls;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController
 * @package AcMarche\Mercredi\Controller
 *
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 * @Route("/export")
 */
class ExportController extends AbstractController
{
    /**
     * @var GeneratorXls
     */
    private $generatorXls;

    public function __construct(GeneratorXls $generatorXls)
    {
        $this->generatorXls = $generatorXls;
    }

    /**
     * @Route("/", name="mercredi_admin_export_presence_xls")
     */
    public function default(Request $request): Response
    {
        return $this->render(
            '@AcMarcheMercrediAdmin/default/index.html.twig',
            [
            ]
        );
    }
}
