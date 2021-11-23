<?php


namespace AcMarche\Mercredi\Facture\Render;


use AcMarche\Mercredi\Entity\Facture\FacturePresence;
use AcMarche\Mercredi\Facture\FactureInterface;
use AcMarche\Mercredi\Facture\Repository\FacturePresenceRepository;
use AcMarche\Mercredi\Facture\Utils\FactureUtils;
use AcMarche\Mercredi\Organisation\Repository\OrganisationRepository;
use Twig\Environment;

class FactureRender
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

    public function generateOneHtml(FactureInterface $facture): string
    {
        $content = $this->prepareContent($facture);

        return $this->environment->render(
            '@AcMarcheMercrediAdmin/facture/hotton/pdf.html.twig',
            [
                'content' => $content,
            ]
        );
    }

    /**
     * @param array $factures
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function generateMultipleHtml(array $factures): string
    {
        $content = '';
        foreach ($factures as $facture) {
            $content .= $this->prepareContent($facture);
            $content .= '<div class="page-breaker"></div>';
        }

        return $this->environment->render(
            '@AcMarcheMercrediAdmin/facture/hotton/pdf.html.twig',
            [
                'content' => $content,
            ]
        );
    }

    private function prepareContent(FactureInterface $facture): string
    {
        $organisation = $this->organisationRepository->getOrganisation();
        $data = [
            'enfants' => [],
            'cout' => 0,
        ];
        //init
        foreach ($this->factureUtils->getEnfants($facture) as $slug => $enfant) {
            $data['enfants'][$slug] = [
                'enfant' => $enfant,
                'cout' => 0,
                'peda' => 0,
                'mercredi' => 0,
                'accueils' => [
                    'Soir' => ['nb' => 0, 'cout' => 0],
                    'Matin' => ['nb' => 0, 'cout' => 0],
                ],
            ];
        }

        $tuteur = $facture->getTuteur();
        $facturePresences = $this->facturePresenceRepository->findByFactureAndType(
            $facture,
            FactureInterface::OBJECT_PRESENCE
        );
        $factureAccueils = $this->facturePresenceRepository->findByFactureAndType(
            $facture,
            FactureInterface::OBJECT_ACCUEIL
        );

        foreach ($facturePresences as $facturePresence) {
            $data = $this->groupPresences($facturePresence, $data);
        }

        foreach ($factureAccueils as $factureAccueil) {
            $data = $this->groupAccueils($factureAccueil, $data);
        }

        foreach ($data['enfants'] as $enfant) {
            $data['cout'] += $enfant['cout'];
        }

        return $this->environment->render(
            '@AcMarcheMercrediAdmin/facture/hotton/_content_pdf.html.twig',
            [
                'facture' => $facture,
                'tuteur' => $tuteur,
                'organisation' => $organisation,
                'data' => $data,
            ]
        );
    }

    private function groupAccueils(FacturePresence $facturePresence, array $data): array
    {
        $enfant = $facturePresence->getNom().' '.$facturePresence->getPrenom();
        $slug = $this->factureUtils->slugger->slug($enfant);
        $heure = $facturePresence->getHeure();
        $duree = $facturePresence->getDuree();
        $data['enfants'][$slug->toString()]['cout'] += $facturePresence->getCoutCalculated();
        $data['enfants'][$slug->toString()]['accueils'][$heure]['nb'] += $duree;

        return $data;
    }

    private function groupPresences(FacturePresence $facturePresence, array $data): array
    {
        $enfant = $facturePresence->getNom().' '.$facturePresence->getPrenom();
        $slug = $this->factureUtils->slugger->slug($enfant);
        if ($facturePresence->isPedagogique()) {
            $data['enfants'][$slug->toString()]['peda'] += 1;
        }
        if (!$facturePresence->isPedagogique()) {
            $data['enfants'][$slug->toString()]['mercredi'] += 1;
        }
        $data['enfants'][$slug->toString()]['cout'] += $facturePresence->getCoutCalculated();

        return $data;
    }
}
