<?php

namespace AcMarche\Mercredi\Controller\Parent;

use AcMarche\Mercredi\Attestation\AttestationGenerator;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Organisation\Traits\OrganisationPropertyInitTrait;
use AcMarche\Mercredi\Pdf\PdfDownloaderTrait;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route(path: '/attestation')]
final class AttestationController extends AbstractController
{
    use GetTuteurTrait, OrganisationPropertyInitTrait, PdfDownloaderTrait;

    public function __construct(
        private RelationRepository $relationRepository,
        private PresenceRepository $presenceRepository,
        private AttestationGenerator $attestationGenerator,
    ) {
    }

    #[Route(path: '/{year}/{uuid}', name: 'mercredi_parent_attestation')]
    #[IsGranted('enfant_show', subject: 'enfant')]
    public function default(int $year, Enfant $enfant): Response
    {
        if (($hasTuteur = $this->hasTuteur()) !== null) {
            return $hasTuteur;
        }
        $presences = $this->presenceRepository->findByTuteurAndEnfantAndYear($this->tuteur, $enfant, $year);
        if (count($presences) === 0) {
            $this->addFlash('danger', 'Aucune présence en '.$year.' pour cette enfant');

            return $this->redirectToRoute('mercredi_parent_tuteur_show');
        }

        $data = $this->attestationGenerator->newOne($presences);
        $html = $this->renderView('@AcMarcheMercredi/admin/attestation/one/2022.html.twig', [
            'data' => $data,
            'tuteur' => $this->tuteur,
            'enfant' => $enfant,
            'year' => $year,
            'today' => new \DateTime(),
            'organisation' => $this->organisation,
        ]);

     //   return new Response($html);

        return $this->downloadPdf($html, 'attestation-'.$enfant->getSlug().'-'.$year.'.pdf');
    }
}
