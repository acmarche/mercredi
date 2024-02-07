<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Attestation\XlsGenerator;
use AcMarche\Mercredi\Contrat\Attestation\AttestationGeneratorInterface;
use AcMarche\Mercredi\Contrat\Facture\FactureCalculatorInterface;
use AcMarche\Mercredi\Contrat\Presence\PresenceCalculatorInterface;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Facture\Repository\FacturePresenceRepository;
use AcMarche\Mercredi\Facture\Repository\FactureRepository;
use AcMarche\Mercredi\Facture\Utils\FactureUtils;
use AcMarche\Mercredi\Jour\Repository\JourRepository;
use AcMarche\Mercredi\Migration\PaiementRepository;
use AcMarche\Mercredi\Organisation\Traits\OrganisationPropertyInitTrait;
use AcMarche\Mercredi\Pdf\PdfDownloaderTrait;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Relation\Utils\OrdreService;
use AcMarche\Mercredi\Spreadsheet\SpreadsheetDownloaderTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/attestation')]
#[IsGranted('ROLE_MERCREDI_ADMIN')]
final class AttestationController extends AbstractController
{
    use PdfDownloaderTrait, OrganisationPropertyInitTrait, SpreadsheetDownloaderTrait;

    public function __construct(
        private JourRepository $jourRepository,
        private PresenceRepository $presenceRepository,
        private PaiementRepository $paiementRepository,
        private FacturePresenceRepository $facturePresenceRepository,
        private PresenceCalculatorInterface $presenceCalculator,
        private OrdreService $ordreService,
        private FactureCalculatorInterface $factureCalculator,
        private XlsGenerator $xlsGenerator,
        private AttestationGeneratorInterface $attestationGenerator,
        private FactureRepository $factureRepository,
        private FactureUtils $factureUtils,
    ) {
    }

    #[Route(path: '/', name: 'mercredi_admin_attestation_index')]
    public function default(): Response
    {
        return $this->render(
            '@AcMarcheMercredi/admin/attestation/index.html.twig',
            [
            ]
        );
    }

    #[Route('/attestation/{tuteur}/{enfant}/{year}', name: 'mercredi_admin_attestation_new')]
    public function index(Tuteur $tuteur, Enfant $enfant, int $year): Response
    {
        if ($this->attestationGenerator->hasAttestation($tuteur, $enfant, $year)) {
            $this->addFlash('danger', 'Aucune présence payée en '.$year.' pour cette enfant');

            return $this->redirectToRoute('mercredi_admin_tuteur_show', ['id' => $tuteur->getId()]);
        }

        $html = $this->attestationGenerator->renderOne($tuteur, $enfant, $year);

        return new Response($html);

        return $this->downloadPdf($html, 'attestation-'.$enfant->getSlug().'-'.$year.'.pdf');
    }

    #[Route(path: '/{year}', name: 'mercredi_admin_attestation_spf')]
    public function spf(int $year): Response
    {
        $spreadSheet = $this->xlsGenerator->forSpf($year);

        return $this->downloadXls($spreadSheet, 'spf-'.$year.'.xls');
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

}