<?php

namespace AcMarche\Mercredi\Facture\Render;

use AcMarche\Mercredi\Contrat\Facture\FacturePdfPlaineInterface;
use AcMarche\Mercredi\Facture\FactureInterface;
use AcMarche\Mercredi\Organisation\Repository\OrganisationRepository;
use AcMarche\Mercredi\Plaine\Repository\PlainePresenceRepository;
use AcMarche\Mercredi\Relation\Utils\RelationUtils;
use Twig\Environment;

class FacturePdfPlaineHotton implements FacturePdfPlaineInterface
{
    private OrganisationRepository $organisationRepository;
    private PlainePresenceRepository $plainePresenceRepository;
    private RelationUtils $relationUtils;

    public function __construct(
        OrganisationRepository $organisationRepository,
        RelationUtils $relationUtils,
        PlainePresenceRepository $plainePresenceRepository,
        Environment $environment
    ) {
        $this->environment = $environment;
        $this->organisationRepository = $organisationRepository;
        $this->plainePresenceRepository = $plainePresenceRepository;
        $this->relationUtils = $relationUtils;
    }

    public function render(FactureInterface $facture): string
    {
        $content = $this->prepareContent($facture);

        return $this->environment->render(
            '@AcMarcheMercrediAdmin/facture/hotton/pdf.html.twig',
            [
                'content' => $content,
            ]
        );
    }

    private function prepareContent(FactureInterface $facture): string
    {
        $plaine = $facture->getPlaine();
        $tuteur = $facture->getTuteur();
        $organisation = $this->organisationRepository->getOrganisation();

        $inscriptions = $this->plainePresenceRepository->findByPlaineAndTuteur($plaine, $tuteur);
        $enfants = $this->plainePresenceRepository->findEnfantsByPlaineAndTuteur($plaine, $tuteur);

        return $this->environment->render(
            '@AcMarcheMercrediAdmin/facture/hotton/_plaine_content_pdf.html.twig',
            [
                'facture' => $facture,
                'tuteur' => $tuteur,
                'enfants' => $enfants,
                'inscriptions' => $inscriptions,
                'organisation' => $organisation,
                'plaine' => $plaine,
            ]
        );

    }

}
