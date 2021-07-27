<?php


namespace AcMarche\Mercredi\FacturePlaine\Handler;


use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Entity\Facture\FacturePresence;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Facture\Factory\CommunicationFactory;
use AcMarche\Mercredi\Facture\Factory\FactureFactory;
use AcMarche\Mercredi\Facture\FactureInterface;
use AcMarche\Mercredi\Facture\Repository\FacturePresenceRepository;
use AcMarche\Mercredi\Facture\Repository\FactureRepository;
use AcMarche\Mercredi\Facture\Utils\FactureUtils;
use AcMarche\Mercredi\Organisation\Repository\OrganisationRepository;
use AcMarche\Mercredi\Plaine\Calculator\PlaineCalculatorInterface;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use Twig\Environment;

class FactureHandler
{
    private FactureFactory $facturePlaineFactory;
    private PresenceRepository $presenceRepository;
    private CommunicationFactory $communicationFactory;
    private PlaineCalculatorInterface $plaineCalculator;
    private FacturePresenceRepository $facturePresenceRepository;
    private FactureRepository $factureRepository;
    private Environment $environment;
    private OrganisationRepository $organisationRepository;
    private FactureUtils $factureUtils;

    public function __construct(
        FactureFactory $facturePlaineFactory,
        PresenceRepository $presenceRepository,
        CommunicationFactory $communicationFactory,
        PlaineCalculatorInterface $plaineCalculator,
        FacturePresenceRepository $facturePresenceRepository,
        FactureRepository $factureRepository,
        Environment $environment,
        OrganisationRepository $organisationRepository,
        FactureUtils $factureUtils
    ) {
        $this->facturePlaineFactory = $facturePlaineFactory;
        $this->presenceRepository = $presenceRepository;
        $this->communicationFactory = $communicationFactory;
        $this->plaineCalculator = $plaineCalculator;
        $this->facturePresenceRepository = $facturePresenceRepository;
        $this->factureRepository = $factureRepository;
        $this->environment = $environment;
        $this->organisationRepository = $organisationRepository;
        $this->factureUtils = $factureUtils;
    }

    public function newInstance(Tuteur $tuteur): Facture
    {
        return $this->facturePlaineFactory->newInstance($tuteur);
    }

    /**
     * @param Facture $facture
     * @param Plaine $plaine
     * @return Facture
     */
    public function handleManually(Facture $facture, Plaine $plaine): Facture
    {
        $facture->setMois(date('m-Y'));
        $facture->setCommunication($this->communicationFactory->generatePlaine($plaine));
        $tuteur = $facture->getTuteur();
        $presences = $this->presenceRepository->findPresencesByPlaineAndTuteur($plaine, $tuteur);

        $this->attachPresences($facture, $plaine, $presences);

        if (!$facture->getId()) {
            $this->factureRepository->persist($facture);
        }

        $this->flush();

        return $facture;
    }

    private function attachPresences(Facture $facture, Plaine $plaine, array $presences): void
    {
        foreach ($presences as $presence) {
            $facturePresence = new FacturePresence($facture, $presence, FactureInterface::OBJECT_PLAINE);
            $facturePresence->setPresenceDate($presence->getJour()->getDateJour());
            $enfant = $presence->getEnfant();
            $facturePresence->setNom($enfant->getNom());
            $facturePresence->setPrenom($enfant->getPrenom());
            $facturePresence->setCout($this->plaineCalculator->calculateOnePresence($plaine, $presence));
            $this->facturePresenceRepository->persist($facturePresence);
            $facture->addFacturePresence($facturePresence);
        }
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

    private function flush(): void
    {
        //   $this->factureRepository->flush();
        //   $this->facturePresenceRepository->flush();
    }


}
