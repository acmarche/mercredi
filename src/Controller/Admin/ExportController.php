<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Plaine\Factory\PlainePdfFactory;
use AcMarche\Mercredi\Presence\Dto\ListingPresenceByMonth;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Presence\Spreadsheet\SpreadsheetFactory;
use AcMarche\Mercredi\Search\SearchHelper;
use AcMarche\Mercredi\Utils\DateUtils;
use Exception;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[IsGranted('ROLE_MERCREDI_ADMIN')]
#[Route(path: '/export')]
final class ExportController extends AbstractController
{
    public function __construct(
        private SpreadsheetFactory $spreadsheetFactory,
        private ListingPresenceByMonth $listingPresenceByMonth,
        private PresenceRepository $presenceRepository,
        private PlainePdfFactory $plainePdfFactory,
        private SearchHelper $searchHelper
    ) {
    }

    #[Route(path: '/presence', name: 'mercredi_admin_export_presence_xls')]
    public function default(): Response
    {
        $args = $this->searchHelper->getArgs(SearchHelper::PRESENCE_LIST);
        $jour = $args['jour'];
        $ecole = $args['ecole'];
        $presences = $this->presenceRepository->findPresencesByJourAndEcole($jour, $ecole);
        $spreadsheet = $this->spreadsheetFactory->presenceXls($presences);

        return $this->spreadsheetFactory->downloadXls($spreadsheet, 'listing-presences.xls');
    }

    /**
     * @param bool $one Office de l'enfance
     */
    #[Route(path: '/presence/mois/{one}', name: 'mercredi_admin_export_presence_mois_xls', requirements: [
        'mois' => '.+',
    ], methods: ['GET'])]
    public function presenceByMonthXls(bool $one): Response
    {
        $args = $this->searchHelper->getArgs(SearchHelper::PRESENCE_LIST_BY_MONTH);
        $mois = $args['mois'] ?? null;
        $filter = $args['filter'] ?? null;
        if (! $mois) {
            $this->addFlash('danger', 'Indiquez un mois');

            return $this->redirectToRoute('mercredi_admin_presence_by_month');
        }
        try {
            $date = DateUtils::createDateTimeFromDayMonth($mois);
        } catch (Exception $e) {
            $this->addFlash('danger', $e->getMessage());

            return $this->redirectToRoute('mercredi_admin_presence_by_month');
        }
        $fileName = 'listing-'.$date->format('m-Y').'.xls';
        $listingPresences = $this->listingPresenceByMonth->create($date, $filter);
        if ($one) {
            $spreadsheet = $this->spreadsheetFactory->createXlsByMonthForOne($date, $listingPresences);
        } else {
            $spreadsheet = $this->spreadsheetFactory->createXlsByMonthDefault($listingPresences);
        }

        return $this->spreadsheetFactory->downloadXls($spreadsheet, $fileName);
    }

    #[Route(path: '/plaine/{id}/pdf', name: 'mercredi_admin_plaine_pdf')]
    public function plainePdf(Plaine $plaine): Response
    {
        return $this->plainePdfFactory->generate($plaine);
    }
}
