<?php


namespace AcMarche\Mercredi\Facture\Handler;

use AcMarche\Mercredi\Contrat\Plaine\FacturePlaineHandlerInterface;
use AcMarche\Mercredi\Contrat\Plaine\PlaineCalculatorInterface;
use AcMarche\Mercredi\Entity\Facture\FacturePresence;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Facture\Factory\CommunicationFactoryInterface;
use AcMarche\Mercredi\Facture\Factory\FactureFactory;
use AcMarche\Mercredi\Facture\FactureInterface;
use AcMarche\Mercredi\Facture\Repository\FacturePresenceRepository;
use AcMarche\Mercredi\Facture\Repository\FactureRepository;
use AcMarche\Mercredi\Plaine\Repository\PlainePresenceRepository;

class FacturePlaineHandler implements FacturePlaineHandlerInterface
{
    private FactureFactory $factureFactory;
    private CommunicationFactoryInterface $communicationFactory;
    private FactureRepository $factureRepository;
    private PlaineCalculatorInterface $plaineCalculator;
    private FacturePresenceRepository $facturePresenceRepository;
    private PlainePresenceRepository $plainePresenceRepository;

    public function __construct(
        FactureFactory $factureFactory,
        CommunicationFactoryInterface $communicationFactory,
        PlainePresenceRepository $plainePresenceRepository,
        FactureRepository $factureRepository,
        PlaineCalculatorInterface $plaineCalculator,
        FacturePresenceRepository $facturePresenceRepository
    ) {
        $this->factureFactory = $factureFactory;
        $this->communicationFactory = $communicationFactory;
        $this->factureRepository = $factureRepository;
        $this->plaineCalculator = $plaineCalculator;
        $this->facturePresenceRepository = $facturePresenceRepository;
        $this->plainePresenceRepository = $plainePresenceRepository;
    }

    public function newInstance(Plaine $plaine, Tuteur $tuteur): FactureInterface
    {
        $facture = $this->factureFactory->newInstance($tuteur);
        $jours = $plaine->getJours();
        $facture->setMois($jours[0]);
        $facture->setPlaineNom($plaine->getNom());
        $facture->setPlaine($plaine);

        return $facture;
    }

    /**
     * @param FactureInterface $facture
     * @param Plaine $plaine
     * @return FactureInterface
     */
    public function handleManually(FactureInterface $facture, Plaine $plaine): FactureInterface
    {
        $facture->setCommunication($this->communicationFactory->generateForPlaine($plaine, $facture));
        $tuteur = $facture->getTuteur();
        $presences = $this->plainePresenceRepository->findByPlaineAndTuteur($plaine, $tuteur);

        $this->attachPresences($facture, $plaine, $presences);
        $this->factureFactory->setEcoles($facture);

        if (!$facture->getId()) {
            $this->factureRepository->persist($facture);
        }

        $this->flush();

        return $facture;
    }

    private function attachPresences(FactureInterface $facture, Plaine $plaine, array $presences): void
    {
        foreach ($presences as $presence) {
            if (!$facturePresence = $this->facturePresenceRepository->findByIdAndType(
                $presence->getId(),
                FactureInterface::OBJECT_PLAINE
            )) {
                $facturePresence = new FacturePresence($facture, $presence->getId(), FactureInterface::OBJECT_PLAINE);
                $this->facturePresenceRepository->persist($facturePresence);
            }

            $facturePresence->setPresenceDate($presence->getJour()->getDateJour());
            $enfant = $presence->getEnfant();
            if ($ecole = $enfant->getEcole()) {
                $facture->ecolesListing[$ecole->getId()] = $ecole;
            }
            $facturePresence->setNom($enfant->getNom());
            $facturePresence->setPrenom($enfant->getPrenom());
            $ordre = $this->plaineCalculator->getOrdreOnePresence($presence);
            $facturePresence->setCoutBrut($this->plaineCalculator->getPrixByOrdre($plaine, $ordre));
            $facturePresence->setCoutCalculated($this->plaineCalculator->calculateOnePresence($plaine, $presence));
        }
    }

    private function flush(): void
    {
        $this->factureRepository->flush();
    }
}
