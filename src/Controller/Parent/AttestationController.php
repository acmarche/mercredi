<?php

namespace AcMarche\Mercredi\Controller\Parent;

use Symfony\Component\HttpFoundation\Response;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Pdf\PdfDownloaderTrait;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;
use AcMarche\Mercredi\Relation\Utils\RelationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController.
 *
 * @Route("/attestation")
 */
final class AttestationController extends AbstractController
{
    use GetTuteurTrait;
    use PdfDownloaderTrait;
    public RelationRepository $relationRepository;

    public function __construct(RelationRepository $relationRepository)
    {
        $this->relationRepository = $relationRepository;
    }

    /**
     * @Route("/{year}/{uuid}", name="mercredi_parent_attestation")
     * @IsGranted("ROLE_MERCREDI_PARENT")
     */
    public function default(int $year, Enfant $enfant): Response
    {
        if (($hasTuteur = $this->hasTuteur()) !== null) {
            return $hasTuteur;
        }
        $relations = $this->relationRepository->findByTuteur($this->tuteur);
        $enfants = RelationUtils::extractEnfants($relations);
        $factures = [];
        $html = $this->render(
            '@AcMarcheMercredi/commun/attestation/index.html.Twig',
            [
                'enfants' => $enfants,
                'factures' => $factures,
                'tuteur' => $this->tuteur,
                'year' => $year,
            ]
        );

        return $this->downloadPdf($html, $enfant->getSlug().'-attestation-'.$year.'.pdf');
    }
}
