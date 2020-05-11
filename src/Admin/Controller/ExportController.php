<?php

namespace AcMarche\Mercredi\Admin\Controller;

use AcMarche\Mercredi\Presence\Dto\ListingPresenceByMonth;
use AcMarche\Mercredi\Presence\Spreadsheet\SpreadsheetFactory;
use AcMarche\Mercredi\Search\SearchHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * @var SpreadsheetFactory
     */
    private $spreadsheetFactory;
    /**
     * @var ListingPresenceByMonth
     */
    private $listingPresenceByMonth;
    /**
     * @var SearchHelper
     */
    private $searchHelper;

    public function __construct(
        SpreadsheetFactory $spreadsheetFactory,
        ListingPresenceByMonth $listingPresenceByMonth,
        SearchHelper $searchHelper
    ) {
        $this->spreadsheetFactory = $spreadsheetFactory;
        $this->listingPresenceByMonth = $listingPresenceByMonth;
        $this->searchHelper = $searchHelper;
    }

    /**
     * @Route("/presence", name="mercredi_admin_export_presence_xls")
     */
    public function default(Request $request): Response
    {
        $args = $this->searchHelper->getArgs(SearchHelper::PRESENCE_LIST);
        dump($args);
        $date = $args['date'];
        $listingPresences = $this->listingPresenceByMonth->create($date);
        $spreadsheet = $this->spreadsheetFactory->presenceXls($listingPresences);

        return $this->spreadsheetFactory->downloadXls($spreadsheet, 'presences.xls');
    }

    /**
     * @Route("/presence/mois/{mois}/{one}", name="mercredi_admin_export_presence_mois_xls", requirements={"mois"=".+"}, methods={"GET"})
     * Requirement a cause du format "mois/annee"
     *
     * @param $mois
     * @param bool $one Office de l'enfance
     *
     */
    public function xls(string $mois, bool $one): Response
    {
        $args = $this->searchHelper->getArgs(SearchHelper::PRESENCE_LIST_BY_MONTH);
        dump($args);
        $date = $args['date'];

        $fileName = 'listing-'.preg_replace('#/#', '-', $mois).'.xls';

        $listingPresences = $this->listingPresenceByMonth->create($date);

        if ($one) {
            $spreadsheet = $this->spreadsheetFactory->createXSLOne($mois, $listingPresences);
        } else {
            $spreadsheet = $this->spreadsheetFactory->createXSLObject($listingPresences);
        }

        return $this->spreadsheetFactory->downloadXls($spreadsheet, $fileName);
    }
}
