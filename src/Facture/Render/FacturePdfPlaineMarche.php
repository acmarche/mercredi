<?php

namespace AcMarche\Mercredi\Facture\Render;

use AcMarche\Mercredi\Contrat\Facture\FactureCalculatorInterface;
use AcMarche\Mercredi\Contrat\Facture\FacturePdfPlaineInterface;
use AcMarche\Mercredi\Facture\FactureInterface;
use AcMarche\Mercredi\Organisation\Repository\OrganisationRepository;
use AcMarche\Mercredi\Plaine\Repository\PlainePresenceRepository;
use Twig\Environment;

class FacturePdfPlaineMarche implements FacturePdfPlaineInterface
{
    public function __construct(
        private OrganisationRepository $organisationRepository,
        private FactureCalculatorInterface $factureCalculator,
        private PlainePresenceRepository $plainePresenceRepository,
        private Environment $environment
    ) {
    }

    public function render(FactureInterface $facture): string
    {
        $content = $this->prepareContent($facture);

        return $this->environment->render(
            '@AcMarcheMercrediAdmin/facture/marche/pdf.html.twig',
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

        $dto = $this->factureCalculator->createDetail($facture);
        $inscriptions = $this->plainePresenceRepository->findByPlaineAndTuteur($plaine, $tuteur);
        $enfants = $this->plainePresenceRepository->findEnfantsByPlaineAndTuteur($plaine, $tuteur);

        return $this->environment->render(
            '@AcMarcheMercrediAdmin/facture/marche/_plaine_content_pdf.html.twig',
            [
                'facture' => $facture,
                'tuteur' => $tuteur,
                'enfants' => $enfants,
                'inscriptions' => $inscriptions,
                'organisation' => $organisation,
                'dto' => $dto,
                'plaine' => $plaine,
            ]
        );
    }
}
