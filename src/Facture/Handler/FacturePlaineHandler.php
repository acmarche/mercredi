<?php


namespace AcMarche\Mercredi\Facture\Handler;


use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Entity\Facture\FacturePresence;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Facture\Factory\CommunicationFactory;
use AcMarche\Mercredi\Facture\Factory\FactureFactory;
use AcMarche\Mercredi\Facture\FactureInterface;
use AcMarche\Mercredi\Facture\Repository\FacturePresenceRepository;
use AcMarche\Mercredi\Facture\Repository\FactureRepository;
use AcMarche\Mercredi\Plaine\Calculator\PlaineCalculatorInterface;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;

class FacturePlaineHandler
{
    private FactureFactory $factureFactory;
    private CommunicationFactory $communicationFactory;
    private PresenceRepository $presenceRepository;
    private FactureRepository $factureRepository;
    private PlaineCalculatorInterface $plaineCalculator;
    private FacturePresenceRepository $facturePresenceRepository;
    private array $ecoles = [];

    public function __construct(
        FactureFactory $factureFactory,
        CommunicationFactory $communicationFactory,
        PresenceRepository $presenceRepository,
        FactureRepository $factureRepository,
        PlaineCalculatorInterface $plaineCalculator,
        FacturePresenceRepository $facturePresenceRepository
    ) {
        $this->factureFactory = $factureFactory;
        $this->communicationFactory = $communicationFactory;
        $this->presenceRepository = $presenceRepository;
        $this->factureRepository = $factureRepository;
        $this->plaineCalculator = $plaineCalculator;
        $this->facturePresenceRepository = $facturePresenceRepository;
    }

    public function newInstance(Tuteur $tuteur): Facture
    {
        return $this->factureFactory->newInstance($tuteur);
    }

    /**
     * @param Facture $facture
     * @param Plaine $plaine
     * @return Facture
     */
    public function handleManually(Facture $facture, Plaine $plaine): Facture
    {
        $facture->setMois(date('m-Y'));
        $facture->setPlaine($plaine->getNom());
        $facture->setCommunication($this->communicationFactory->generatePlaine($plaine));
        $tuteur = $facture->getTuteur();
        $presences = $this->presenceRepository->findByPlaineAndTuteur($plaine, $tuteur);

        $this->attachPresences($facture, $plaine, $presences);
        $this->factureFactory->setEcoles($facture,$this->ecoles);

        if (!$facture->getId()) {
            $this->factureRepository->persist($facture);
        }

        $this->flush();

        return $facture;
    }

    private function attachPresences(Facture $facture, Plaine $plaine, array $presences): void
    {
        foreach ($presences as $presence) {
            $facturePresence = new FacturePresence($facture, $presence->getId(), FactureInterface::OBJECT_PLAINE);
            $facturePresence->setPresenceDate($presence->getJour()->getDateJour());
            $enfant = $presence->getEnfant();
            $this->ecoles[] = $enfant->getEcole()->getNom();
            $facturePresence->setNom($enfant->getNom());
            $facturePresence->setPrenom($enfant->getPrenom());
            $facturePresence->setCout($this->plaineCalculator->calculateOnePresence($plaine, $presence));
            $this->facturePresenceRepository->persist($facturePresence);
            $facture->addFacturePresence($facturePresence);
        }
    }


    private function flush(): void
    {
        $this->factureRepository->flush();
    }
}
