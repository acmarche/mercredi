<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Facture\Repository\FactureRepository;
use AcMarche\Mercredi\Facture\Utils\FactureUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route(path: '/attestation')]
#[IsGranted(data: 'ROLE_MERCREDI_ADMIN')]
final class AttestationController extends AbstractController
{
    public function __construct(
        private FactureRepository $factureRepository,
        private FactureUtils $factureUtils
    ) {
    }

    #[Route(path: '/', name: 'mercredi_admin_attestation')]
    public function default(): Response
    {
        $year = 2021;
        $factures = $this->factureRepository->findFacturesPaid($year);
        $total = count($factures);
        $data = $this->factureUtils->groupByTuteur($factures);

        foreach ($data as $tuteurId => $row) {
            $data[$tuteurId]['factures_all'] = $this->factureRepository->findByTuteurAndYear($tuteurId, $year);
        }

        return $this->render(
            '@AcMarcheMercredi/admin/attestation/index.html.Twig',
            [
                'data' => $data,
                'year' => $year,
                'total' => $total,
            ]
        );
    }
}
