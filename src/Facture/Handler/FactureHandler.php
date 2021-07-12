<?php

namespace AcMarche\Mercredi\Facture\Handler;

use AcMarche\Mercredi\Accueil\Calculator\AccueilCalculatorInterface;
use AcMarche\Mercredi\Accueil\Repository\AccueilRepository;
use AcMarche\Mercredi\Entity\Accueil;
use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Entity\Facture\FactureAccueil;
use AcMarche\Mercredi\Entity\Facture\FacturePresence;
use AcMarche\Mercredi\Entity\Presence;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Facture\Factory\CommunicationFactory;
use AcMarche\Mercredi\Facture\Factory\FactureFactory;
use AcMarche\Mercredi\Facture\Repository\FactureAccueilRepository;
use AcMarche\Mercredi\Facture\Repository\FacturePresenceRepository;
use AcMarche\Mercredi\Facture\Repository\FactureRepository;
use AcMarche\Mercredi\Presence\Calculator\PresenceCalculatorInterface;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Tuteur\Repository\TuteurRepository;
use Carbon\Carbon;
use Carbon\CarbonInterface;

final class FactureHandler
{
    private FactureRepository $factureRepository;
    private FactureFactory $factureFactory;
    private PresenceCalculatorInterface $presenceCalculator;
    private FacturePresenceRepository $facturePresenceRepository;
    private PresenceRepository $presenceRepository;
    private AccueilRepository $accueilRepository;
    private FactureAccueilRepository $factureAccueilRepository;
    private AccueilCalculatorInterface $accueilCalculator;
    private TuteurRepository $tuteurRepository;
    private CommunicationFactory $communicationFactory;

    public function __construct(
        FactureRepository $factureRepository,
        FacturePresenceRepository $facturePresenceRepository,
        FactureFactory $factureFactory,
        PresenceCalculatorInterface $presenceCalculator,
        PresenceRepository $presenceRepository,
        AccueilRepository $accueilRepository,
        FactureAccueilRepository $factureAccueilRepository,
        AccueilCalculatorInterface $accueilCalculator,
        TuteurRepository $tuteurRepository,
        CommunicationFactory $communicationFactory
    ) {
        $this->factureRepository = $factureRepository;
        $this->factureFactory = $factureFactory;
        $this->presenceCalculator = $presenceCalculator;
        $this->facturePresenceRepository = $facturePresenceRepository;
        $this->presenceRepository = $presenceRepository;
        $this->accueilRepository = $accueilRepository;
        $this->factureAccueilRepository = $factureAccueilRepository;
        $this->accueilCalculator = $accueilCalculator;
        $this->tuteurRepository = $tuteurRepository;
        $this->communicationFactory = $communicationFactory;
    }

    public function newInstance(Tuteur $tuteur): Facture
    {
        return $this->factureFactory->newInstance($tuteur);
    }

    /**
     * @param Facture $facture
     * @param array|int[] $presencesId
     * @param array|int[] $accueilsId
     * @return Facture
     */
    public function handleManually(Facture $facture, array $presencesId, array $accueilsId): Facture
    {
        $presences = $this->presenceRepository->findBy(['id' => $presencesId]);
        $accueils = $this->accueilRepository->findBy(['id' => $accueilsId]);

        $this->finish($facture, $presences, $accueils);
        $facture->setMois(date('m-Y'));
        $this->flush();

        return $facture;
    }

    public function generateByMonth(Tuteur $tuteur, string $month): ?Facture
    {
        list($month, $year) = explode('-', $month);
        $date = Carbon::createFromDate($year, $month, 01);

        $facture = $this->handleByTuteur($tuteur, $date);
        if ($facture) {
            $this->flush();
        }

        return $facture;
    }

    /**
     * @param $month
     * @return array|null
     */
    public function generateByMonthForAll(string $monthSelected): array
    {
        list($month, $year) = explode('-', $monthSelected);
        $date = Carbon::createFromDate($year, $month, 01);
        $factures = [];

        $tuteurs = $this->tuteurRepository->findAllOrderByNom();
        foreach ($tuteurs as $tuteur) {
            if ($facture = $this->handleByTuteur($tuteur, $date)) {
                $factures[] = $facture;
            }
        }

        $this->flush();

        return $factures;
    }

    private function handleByTuteur(Tuteur $tuteur, CarbonInterface $date): ?Facture
    {
        $facture = $this->newInstance($tuteur);
        $facture->setMois($date->format('m-Y'));
        $presences = $this->presenceRepository->findPresencesNonPaysByTuteurAndMonth($tuteur, $date->toDateTime());
        $accueils = $this->accueilRepository->getAccueilsNonPayesByTuteurAndMonth($tuteur, $date->toDateTime());

        if (count($presences) === 0 && count($accueils) === 0) {
            return null;
        }

        $this->finish($facture, $presences, $accueils);

        return $facture;
    }

    /**
     * @param Facture $facture
     * @param array|Presence[] $presences
     * @param array|Accueil[] $accueils
     * @return Facture
     */
    private function finish(Facture $facture, array $presences, array $accueils)
    {
        $facture->setCommunication($this->communicationFactory->generate($facture));
        $this->attachPresences($facture, $presences);
        $this->attachAccueils($facture, $accueils);
        if (!$facture->getId()) {
            $this->factureRepository->persist($facture);
        }

        return $facture;
    }

    /**
     * @param array|Presence[] $presences
     * @param Facture $facture
     */
    private function attachPresences(Facture $facture, array $presences): void
    {
        foreach ($presences as $presence) {
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

    /**
     * @param array|Accueil[] $accueils
     * @param Facture $facture
     */
    private function attachAccueils(Facture $facture, array $accueils): void
    {
        foreach ($accueils as $accueil) {
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

    private function flush()
    {
        $this->factureRepository->flush();
        $this->facturePresenceRepository->flush();
        $this->factureAccueilRepository->flush();
    }

}
