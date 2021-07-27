<?php

namespace AcMarche\Mercredi\Facture\Factory;

use AcMarche\Mercredi\Accueil\Repository\AccueilRepository;
use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Entity\Facture\FacturePresence;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Facture\FactureInterface;
use AcMarche\Mercredi\Facture\Repository\FacturePresenceRepository;
use AcMarche\Mercredi\Facture\Utils\FactureUtils;
use AcMarche\Mercredi\Organisation\Repository\OrganisationRepository;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use DateTime;
use Twig\Environment;

final class FactureFactory
{
    private Environment $environment;
    private OrganisationRepository $organisationRepository;
    private FactureUtils $factureUtils;
    private PresenceRepository $presenceRepository;
    private FacturePresenceRepository $facturePresenceRepository;

    public function __construct(
        Environment $environment,
        OrganisationRepository $organisationRepository,
        FactureUtils $factureUtils,
        PresenceRepository $presenceRepository,
        AccueilRepository $accueilRepository,
        FacturePresenceRepository $facturePresenceRepository
    ) {
        $this->environment = $environment;
        $this->organisationRepository = $organisationRepository;
        $this->factureUtils = $factureUtils;
        $this->presenceRepository = $presenceRepository;
        $this->accueilRepository = $accueilRepository;
        $this->facturePresenceRepository = $facturePresenceRepository;
    }

    public function newInstance(Tuteur $tuteur): Facture
    {
        $facture = new Facture($tuteur);
        $facture->setFactureLe(new DateTime());
        $facture->setNom($tuteur->getNom());
        $facture->setPrenom($tuteur->getPrenom());
        $facture->setRue($tuteur->getRue());
        $facture->setCodePostal($tuteur->getCodePostal());
        $facture->setLocalite($tuteur->getLocalite());

        return $facture;
    }

    public function generateOneHtml(Facture $facture): string
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
        }

        return $this->environment->render(
            '@AcMarcheMercrediAdmin/facture/hotton/pdf.html.twig',
            [
                'content' => $content,
            ]
        );
    }

    private function prepareContent(Facture $facture): string
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
        $ecoles = $this->factureUtils->getEcoles($facture);

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
            '@AcMarcheMercrediAdmin/facture/hotton/_content.html.twig',
            [
                'facture' => $facture,
                'tuteur' => $tuteur,
                'organisation' => $organisation,
                'ecoles' => $ecoles,
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
        $data['enfants'][$slug->toString()]['cout'] += $facturePresence->getCout();
        $data['enfants'][$slug->toString()]['accueils'][$heure]['nb'] += $duree;

        return $data;
    }

    private function groupPresences(FacturePresence $facturePresence, array $data): array
    {
        $presence = $this->presenceRepository->find($facturePresence->getPresenceId());
        $enfant = $facturePresence->getNom().' '.$facturePresence->getPrenom();
        $slug = $this->factureUtils->slugger->slug($enfant);
        if ($presence->getJour()->isPedagogique()) {
            $data['enfants'][$slug->toString()]['peda'] += 1;
        }
        if (!$presence->getJour()->isPedagogique()) {
            $data['enfants'][$slug->toString()]['mercredi'] += 1;
        }
        $data['enfants'][$slug->toString()]['cout'] += $facturePresence->getCout();

        return $data;
    }
}
