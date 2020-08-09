<?php

namespace AcMarche\Mercredi\Facture\Handler;

use AcMarche\Mercredi\Accueil\Calculator\AccueilCalculatorInterface;
use AcMarche\Mercredi\Accueil\Repository\AccueilRepository;
use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Entity\Facture\FactureAccueil;
use AcMarche\Mercredi\Entity\Facture\FacturePresence;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Facture\Factory\FactureFactory;
use AcMarche\Mercredi\Facture\Repository\FactureAccueilRepository;
use AcMarche\Mercredi\Facture\Repository\FacturePresenceRepository;
use AcMarche\Mercredi\Facture\Repository\FactureRepository;
use AcMarche\Mercredi\Presence\Calculator\PresenceCalculatorInterface;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;

final class FactureHandler
{
    /**
     * @var FactureRepository
     */
    private $factureRepository;
    /**
     * @var FactureFactory
     */
    private $factureFactory;
    /**
     * @var PresenceCalculatorInterface
     */
    private $presenceCalculator;
    /**
     * @var FacturePresenceRepository
     */
    private $facturePresenceRepository;
    /**
     * @var PresenceRepository
     */
    private $presenceRepository;
    /**
     * @var AccueilRepository
     */
    private $accueilRepository;
    /**
     * @var FactureAccueilRepository
     */
    private $factureAccueilRepository;
    /**
     * @var AccueilCalculatorInterface
     */
    private $accueilCalculator;

    public function __construct(
        FactureRepository $factureRepository,
        FacturePresenceRepository $facturePresenceRepository,
        FactureFactory $factureFactory,
        PresenceCalculatorInterface $presenceCalculator,
        PresenceRepository $presenceRepository,
        AccueilRepository $accueilRepository,
        FactureAccueilRepository $factureAccueilRepository,
        AccueilCalculatorInterface $accueilCalculator
    ) {
        $this->factureRepository = $factureRepository;
        $this->factureFactory = $factureFactory;
        $this->presenceCalculator = $presenceCalculator;
        $this->facturePresenceRepository = $facturePresenceRepository;
        $this->presenceRepository = $presenceRepository;
        $this->accueilRepository = $accueilRepository;
        $this->factureAccueilRepository = $factureAccueilRepository;
        $this->accueilCalculator = $accueilCalculator;
    }

    public function newInstance(Tuteur $tuteur): Facture
    {
        return $this->factureFactory->newInstance($tuteur);
    }

    /**
     * @param int[] $presencesId
     */
    public function handleNew(Facture $facture, array $presencesId, array $accueilsId): Facture
    {
        $this->handlePresences($presencesId, $facture);
        $this->handleAccueils($accueilsId, $facture);
        if (! $facture->getId()) {
            $this->factureRepository->persist($facture);
        }
        $this->factureRepository->flush();
        $this->facturePresenceRepository->flush();

        return $facture;
    }

    private function handlePresences(array $presencesId, Facture $facture): void
    {
        foreach ($presencesId as $presenceId) {
            if (null === ($presence = $this->presenceRepository->find($presenceId))) {
                continue;
            }
            if (null !== $this->facturePresenceRepository->findByPresence($presence)) {
                continue;
            }
            $facturePresence = new FacturePresence($facture, $presence);
            $facturePresence->setPresenceDate($presence->getJour()->getDateJour());
            $enfant = $presence->getEnfant();
            $facturePresence->setNom($enfant->getNom());
            $facturePresence->setPrenom($enfant->getPrenom());
            $facturePresence->setCout($this->presenceCalculator->calculate($presence));
            $this->facturePresenceRepository->persist($facturePresence);
            $facture->addFacturePresence($facturePresence);
        }
    }

    private function handleAccueils(array $accueilsId, Facture $facture): void
    {
        foreach ($accueilsId as $accueilId) {
            if (null === ($accueil = $this->accueilRepository->find($accueilId))) {
                continue;
            }
            if (null !== $this->factureAccueilRepository->findByAccueil($accueil)) {
                continue;
            }
            $factureAccueil = new FactureAccueil($facture, $accueil);
            $factureAccueil->setAccueilDate($accueil->getDateJour());
            $factureAccueil->setHeure($accueil->getHeure());
            $enfant = $accueil->getEnfant();
            $factureAccueil->setNom($enfant->getNom());
            $factureAccueil->setPrenom($enfant->getPrenom());
            $factureAccueil->setCout($this->accueilCalculator->calculate($accueil));
            $this->factureAccueilRepository->persist($factureAccueil);
            $facture->addFactureAccueil($factureAccueil);
        }
    }
}
