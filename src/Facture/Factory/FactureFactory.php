<?php

namespace AcMarche\Mercredi\Facture\Factory;

use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Entity\Facture\FactureAccueil;
use AcMarche\Mercredi\Entity\Facture\FacturePresence;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Organisation\Repository\OrganisationRepository;
use DateTime;
use Twig\Environment;

final class FactureFactory
{
    private Environment $environment;
    private OrganisationRepository $organisationRepository;

    public function __construct(Environment $environment, OrganisationRepository $organisationRepository)
    {
        $this->environment = $environment;
        $this->organisationRepository = $organisationRepository;
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

    public function generateFullHtml(Facture $facture): string
    {
        $tuteur = $facture->getTuteur();
        $organisation = $this->organisationRepository->getOrganisation();
        $data = [
            'enfants' => [],
            'cout' => 0,
        ];
        //init
        foreach ($facture->getEnfants() as $enfant) {
            $data['enfants'][$enfant->getId()] = [
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

        foreach ($facture->getFactureAccueils() as $factureAccueil) {
            $data = $this->groupAccueils($factureAccueil, $data);
        }

        foreach ($facture->getFacturePresences() as $facturePresence) {
            $data = $this->groupPresences($facturePresence, $data);
        }

        foreach ($data['enfants'] as $enfant) {
            $data['cout'] += $enfant['cout'];
        }

        return $this->environment->render(
            '@AcMarcheMercrediAdmin/facture/hotton/index.html.twig',
            [
                'facture' => $facture,
                'tuteur' => $tuteur,
                'organisation' => $organisation,
                'data' => $data,
            ]
        );
    }

    private function groupAccueils(FactureAccueil $factureAccueil, array $data): array
    {
        $accueil = $factureAccueil->getAccueil();
        $enfant = $accueil->getEnfant();
        $heure = $accueil->getHeure();
        $duree = $accueil->getDuree();
        $data['enfants'][$enfant->getId()]['cout'] += $factureAccueil->getCout();
        $data['enfants'][$enfant->getId()]['accueils'][$heure]['nb'] += $duree;

        return $data;
    }

    private function groupPresences(FacturePresence $facturePresence, array $data): array
    {
        $presence = $facturePresence->getPresence();
        $enfant = $presence->getEnfant();
        if ($presence->getJour()->isPedagogique()) {
            $data['enfants'][$enfant->getId()]['peda'] += 1;
        }
        if (!$presence->getJour()->isPedagogique()) {
            $data['enfants'][$enfant->getId()]['mercredi'] += 1;
        }
        $data['enfants'][$enfant->getId()]['cout'] += $facturePresence->getCout();

        return $data;
    }
}
