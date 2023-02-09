<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Accueil\Repository\AccueilRepository;
use AcMarche\Mercredi\Contrat\Facture\FactureCalculatorInterface;
use AcMarche\Mercredi\Contrat\Presence\PresenceCalculatorInterface;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Facture\Repository\FacturePresenceRepository;
use AcMarche\Mercredi\Jour\Repository\JourRepository;
use AcMarche\Mercredi\Migration\PaiementRepository;
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
        private PaiementRepository $paiementRepository,
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

    #[Route('/attestation/{tuteur}/{enfant}/{year}', name: 'mercredi_admin_one_new')]
    public function index(Tuteur $tuteur, Enfant $enfant, int $year): Response
    {
        $presences = $this->presenceRepository->OneByYear($tuteur, $enfant, $year);
        if (count($presences) === 0) {
            $this->addFlash('danger', 'Aucune présence en '.$year.' pour cette enfant');

            return $this->redirectToRoute('mercredi_admin_tuteur_show', ['id' => $tuteur->getId()]);
        }

        if ($year > 2021) {
            $html = $this->newOne($tuteur, $enfant, $year, $presences);
        } else {
            $html = $this->oldOne($tuteur, $enfant, $year, $presences);
        }

        //   return new Response($html);

        return $this->downloadPdf($html, 'one-'.$enfant->getSlug().'-'.$year.'.pdf');
    }

    private function oldOne(Tuteur $tuteur, Enfant $enfant, int $year, array $presences): string
    {
        $paiments = $this->paiementRepository->getByEnfantTuteur($tuteur, $enfant, $year);
        if (0 == count($paiments)) {
            return 'Aucun paiement en '.$year.'<div class="page-breaker"></div>';
        }

        $presencesPaid = [];
        foreach ($presences as $presence) {
            if ($this->factureCalculator->isPresencePaid($presence)) {
                $presencesPaid[] = $presence;
            }
        }

        $totalPaiement = 0;
        foreach ($paiments as $paiment) {
            $totalPaiement += $paiment->getMontant();
        }

        return $this->renderView('@AcMarcheMercredi/admin/attestation/fiscale/index.html.twig', [
            'tuteur' => $tuteur,
            'enfant' => $enfant,
            'presences' => $presencesPaid,
            'totalpaiement' => $totalPaiement,
            'year' => $year,
            'organisation' => $this->organisation,
        ]);
    }

    private function newOne(Tuteur $tuteur, Enfant $enfant, int $year, array $presences): string
    {
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

        return $this->renderView('@AcMarcheMercredi/admin/attestation/one/2022.html.twig', [
            'data' => $data,
            'tuteur' => $tuteur,
            'enfant' => $enfant,
            'year' => $year,
            'today' => new \DateTime(),
            'organisation' => $this->organisation,
        ]);
    }
}