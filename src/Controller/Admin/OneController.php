<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Accueil\Repository\AccueilRepository;
use AcMarche\Mercredi\Contrat\Facture\FactureCalculatorInterface;
use AcMarche\Mercredi\Contrat\Presence\PresenceCalculatorInterface;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Facture\Repository\FacturePresenceRepository;
use AcMarche\Mercredi\Jour\Repository\JourRepository;
use AcMarche\Mercredi\Organisation\Traits\OrganisationPropertyInitTrait;
use AcMarche\Mercredi\Pdf\PdfDownloaderTrait;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Presence\Utils\PresenceUtils;
use AcMarche\Mercredi\Relation\Utils\OrdreService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/one')]
#[IsGranted('ROLE_MERCREDI_ADMIN')]
final class OneController extends AbstractController
{
    use PdfDownloaderTrait, OrganisationPropertyInitTrait;

    public function __construct(
        private AccueilRepository $accueilRepository,
        private JourRepository $jourRepository,
        private PresenceRepository $presenceRepository,
        private FacturePresenceRepository $facturePresenceRepository,
        private PresenceCalculatorInterface $presenceCalculator,
        private OrdreService $ordreService,
        private FactureCalculatorInterface $factureCalculator
    ) {
    }

    #[Route(path: '/', name: 'mercredi_admin_one')]
    public function default(): Response
    {
        return $this->render(
            '@AcMarcheMercredi/admin/one/index.html.twig',
            [
            ]
        );
    }

    #[Route('/new/{tuteur}/{enfant}/{year}', name: 'mercredi_admin_one_new')]
    public function index(Tuteur $tuteur, Enfant $enfant, int $year): Response
    {
        $presences = $this->presenceRepository->OneByYear($tuteur, $enfant, $year);
        if (count($presences) === 0) {
            $this->addFlash('danger', 'Aucune présence en '.$year.' pour cette enfant');

            return $this->redirectToRoute('mercredi_admin_tuteur_show', ['id' => $tuteur->getId()]);
        }

        $presencesPaid = [];
        foreach ($presences as $presence) {
            if ($this->factureCalculator->isPresencePaid($presence)) {
                $presencesPaid[] = $presence;
            }
        }

        foreach ($presencesPaid as $presence) {
            $presence->cout = $this->presenceCalculator->calculate($presence);
        }

        $quarters = PresenceUtils::groupByQuarter($presencesPaid);
        $dates = [
            1 => '01/01/'.$year.' => 31/03/'.$year,
            2 => '01/04/'.$year.' => 31/06/'.$year,
            3 => '01/07/'.$year.' => 31/09/'.$year,
            4 => '01/10/'.$year.' => 31/12/'.$year,
        ];
        $data = [
            1 => ['total' => 0, 'presences' => []],
            2 => ['total' => 0, 'presences' => []],
            3 => ['total' => 0, 'presences' => []],
            4 => ['total' => 0, 'presences' => []],
        ];

        foreach ($quarters as $key => $row) {
            $data[$key]['dates'] = $dates[$key];
            foreach ($row as $item) {
                $data[$key]['presences'][] = $item;
                $data[$key]['total'] += $item->cout;
            }
        }

        foreach ($data as $key => $row) {
            if (count($row['presences']) > 0) {
                $data[$key]['prix'] = $row['total'] / count($row['presences']);
            } else {
                $data[$key]['prix'] = 0;
            }
        }

        $html = $this->renderView('@AcMarcheMercredi/admin/one/new.html.twig', [
            'data' => $data,
            'tuteur' => $tuteur,
            'enfant' => $enfant,
            'year' => $year,
            'today' => new \DateTime(),
            'signature' => null,
            'organisation' => $this->organisation,
        ]);

        return $this->downloadPdf($html, 'one-'.$enfant->getSlug().'-'.$year.'.pdf');
    }
}
