<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Pdf\PdfDownloaderTrait;
use AcMarche\Mercredi\Plaine\Repository\PlainePresenceRepository;
use AcMarche\Mercredi\Plaine\Utils\PlaineUtils;
use AcMarche\Mercredi\Presence\Dto\ListingPresenceByMonth;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Presence\Spreadsheet\SpreadsheetFactory;
use AcMarche\Mercredi\Scolaire\Grouping\GroupingInterface;
use AcMarche\Mercredi\Search\SearchHelper;
use AcMarche\Mercredi\Utils\DateUtils;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController.
 *
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 * @Route("/export")
 */
final class ExportController extends AbstractController
{
    use PdfDownloaderTrait;

    private SpreadsheetFactory $spreadsheetFactory;
    private ListingPresenceByMonth $listingPresenceByMonth;
    private SearchHelper $searchHelper;
    private PresenceRepository $presenceRepository;
    private PlainePresenceRepository $plainePresenceRepository;
    private GroupingInterface $grouping;

    public function __construct(
        SpreadsheetFactory $spreadsheetFactory,
        ListingPresenceByMonth $listingPresenceByMonth,
        PresenceRepository $presenceRepository,
        PlainePresenceRepository $plainePresenceRepository,
        GroupingInterface $grouping,
        SearchHelper $searchHelper
    ) {
        $this->spreadsheetFactory = $spreadsheetFactory;
        $this->listingPresenceByMonth = $listingPresenceByMonth;
        $this->searchHelper = $searchHelper;
        $this->presenceRepository = $presenceRepository;
        $this->plainePresenceRepository = $plainePresenceRepository;
        $this->grouping = $grouping;
    }

    /**
     * @Route("/presence", name="mercredi_admin_export_presence_xls")
     */
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
     * @Route("/presence/mois/{one}", name="mercredi_admin_export_presence_mois_xls", requirements={"mois"=".+"}, methods={"GET"})
     * Requirement a cause du format "mois/annee"
     *
     * @param bool $one Office de l'enfance
     */
    public function presenceByMonthXls(bool $one): Response
    {
        $args = $this->searchHelper->getArgs(SearchHelper::PRESENCE_LIST_BY_MONTH);

        $mois = $args['mois'] ?? null;
        if (!$mois) {
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

        $listingPresences = $this->listingPresenceByMonth->create($date);

        if ($one) {
            $spreadsheet = $this->spreadsheetFactory->createXlsByMonthForOne($date, $listingPresences);
        } else {
            $spreadsheet = $this->spreadsheetFactory->createXlsByMonthDefault($listingPresences);
        }

        return $this->spreadsheetFactory->downloadXls($spreadsheet, $fileName);
    }

    /**
     * @Route("/plaine/{id}/pdf", name="mercredi_admin_plaine_pdf")
     */
    public function plainePdf(Plaine $plaine): Response
    {
        $images = $this->getImagesBase64();

        $presences = $this->presenceRepository->findPresencesByPlaine($plaine);
        /**
         * par enfant je dois avoir quel tuteur en garde
         * jours presents
         */
        $data = [];
        foreach ($presences as $presence) {
            $enfant = $presence->getEnfant();
            $tuteur = $presence->getTuteur();
            $jour = $presence->getJour();
            $enfantId = $enfant->getId();
            $data[$enfantId]['enfant'] = $enfant;
            $data[$enfantId]['tuteur'] = $tuteur;
            $data[$enfantId]['jours'][] = $jour;
        }
        dump($data);

        $dates = PlaineUtils::extractJoursFromPlaine($plaine);

        $html = $this->renderView(
            '@AcMarcheMercrediAdmin/plaine/pdf/plaine.html.twig',
            [
                'plaine' => $plaine,
                'dates' => $dates,
                'datas' => $data,
                'images' => $images,
            ]
        );

        return new Response($html);

        $name = $plaine->getSlug();

        $this->pdf->setOption('footer-right', '[page]/[toPage]');
        if (count($dates) > 6) {
            $this->pdf->setOption('orientation', 'landscape');
        }

        return $this->downloadPdf($html, $name.'.pdf');
    }

    private function getImagesBase64()
    {
        $root = $this->getParameter('kernel.project_dir').'/public/bundles/acmarchemercredi/images/';
        $ok = $root.'check_ok.jpg';
        $ko = $root.'check_ko.jpg';
        $data = [];
        $data['ok'] = base64_encode(file_get_contents($ok));
        $data['ko'] = base64_encode(file_get_contents($ko));

        return $data;
    }
}
