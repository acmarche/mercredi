<?php


namespace AcMarche\Mercredi\Facture\Render;


use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Entity\Facture\FacturePresence;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Facture\FactureInterface;
use AcMarche\Mercredi\Facture\Repository\FacturePresenceRepository;
use AcMarche\Mercredi\Facture\Utils\FactureUtils;
use AcMarche\Mercredi\Organisation\Repository\OrganisationRepository;
use Twig\Environment;

class FacturePlaineRender
{
    private Environment $environment;
    private OrganisationRepository $organisationRepository;
    private FactureUtils $factureUtils;
    private FacturePresenceRepository $facturePresenceRepository;

    public function __construct(
        Environment $environment,
        OrganisationRepository $organisationRepository,
        FactureUtils $factureUtils,
        FacturePresenceRepository $facturePresenceRepository
    ) {
        $this->environment = $environment;
        $this->organisationRepository = $organisationRepository;
        $this->factureUtils = $factureUtils;
        $this->facturePresenceRepository = $facturePresenceRepository;
    }


    public function generateOneHtml(Facture $facture, Plaine $plaine): string
    {
        $content = $this->prepareContent($facture, $plaine);

        return $this->environment->render(
            '@AcMarcheMercrediAdmin/facture/hotton/pdf.html.twig',
            [
                'content' => $content,
            ]
        );
    }

    private function prepareContent(Facture $facture, Plaine $plaine): string
    {
        $tuteur = $facture->getTuteur();
        $organisation = $this->organisationRepository->getOrganisation();
        $data = [
            'enfants' => [],
            'cout' => 0,
        ];
        //init
        foreach ($this->factureUtils->getEnfants($facture) as $enfant) {
            $data['enfants'][$enfant->getId()] = [
                'enfant' => $enfant,
                'cout' => 0,
            ];
        }

        return $this->environment->render(
            '@AcMarcheMercrediAdmin/facture/plaine/hotton/_content.html.twig',
            [
                'facture' => $facture,
                'plaine' => $plaine,
                'tuteur' => $tuteur,
                'organisation' => $organisation,
                'data' => $data,
            ]
        );
    }
}
