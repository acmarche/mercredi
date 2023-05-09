<?php

namespace AcMarche\Mercredi\Controller\Parent;

use AcMarche\Mercredi\Accueil\Repository\AccueilRepository;
use AcMarche\Mercredi\Contrat\Attestation\AttestationGeneratorInterface;
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
        private AccueilRepository $accueilRepository,
        private AttestationGeneratorInterface $attestationGenerator,
    ) {
    }

    #[Route(path: '/{year}/{uuid}', name: 'mercredi_parent_attestation')]
    #[IsGranted('enfant_show', subject: 'enfant')]
    public function default(int $year, Enfant $enfant): Response
    {
        if (($hasTuteur = $this->hasTuteur()) !== null) {
            return $hasTuteur;
        }

        if ($this->attestationGenerator->hasAttestation($this->tuteur, $enfant, $year)) {
            $this->addFlash('danger', 'Aucune présence en '.$year.' pour cette enfant');

            return $this->redirectToRoute('mercredi_parent_tuteur_show');
        }

        $html = $this->attestationGenerator->renderOne($this->tuteur, $enfant, $year);

        //   return new Response($html);

        return $this->downloadPdf($html, 'attestation-'.$enfant->getSlug().'-'.$year.'.pdf');
    }
}
