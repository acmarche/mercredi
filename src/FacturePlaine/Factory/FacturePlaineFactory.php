<?php


namespace AcMarche\Mercredi\FacturePlaine\Factory;


use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Organisation\Repository\OrganisationRepository;
use DateTime;
use Twig\Environment;

class FacturePlaineFactory
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

}
