<?php

namespace AcMarche\Mercredi\Facture\Handler;

use DateTime;
use AcMarche\Mercredi\Accueil\Calculator\AccueilCalculatorInterface;
use AcMarche\Mercredi\Accueil\Repository\AccueilRepository;
use AcMarche\Mercredi\Contrat\Facture\FactureHandlerInterface;
use AcMarche\Mercredi\Contrat\Presence\PresenceCalculatorInterface;
use AcMarche\Mercredi\Contrat\Presence\PresenceInterface;
use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Entity\Facture\FactureComplement;
use AcMarche\Mercredi\Entity\Facture\FacturePresence;
use AcMarche\Mercredi\Entity\Presence\Accueil;
use AcMarche\Mercredi\Entity\Presence\Presence;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Facture\Factory\CommunicationFactoryInterface;
use AcMarche\Mercredi\Facture\Factory\FactureFactory;
use AcMarche\Mercredi\Facture\FactureInterface;
use AcMarche\Mercredi\Facture\Repository\FacturePresenceNonPayeRepository;
use AcMarche\Mercredi\Facture\Repository\FacturePresenceRepository;
use AcMarche\Mercredi\Facture\Repository\FactureRepository;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Tuteur\Repository\TuteurRepository;
use Carbon\Carbon;
use Carbon\CarbonInterface;

final class FactureHandler implements FactureHandlerInterface
{
    private FactureRepository $factureRepository;
    private FactureFactory $factureFactory;
    private PresenceCalculatorInterface $presenceCalculator;
    private FacturePresenceRepository $facturePresenceRepository;
    private PresenceRepository $presenceRepository;
    private AccueilRepository $accueilRepository;
    private AccueilCalculatorInterface $accueilCalculator;
    private TuteurRepository $tuteurRepository;
    private CommunicationFactoryInterface $communicationFactory;
    private FacturePresenceNonPayeRepository $facturePresenceNonPayeRepository;

    public function __construct(
        FactureRepository $factureRepository,
        FacturePresenceRepository $facturePresenceRepository,
        FactureFactory $factureFactory,
        PresenceCalculatorInterface $presenceCalculator,
        PresenceRepository $presenceRepository,
        AccueilRepository $accueilRepository,
        AccueilCalculatorInterface $accueilCalculator,
        TuteurRepository $tuteurRepository,
        CommunicationFactoryInterface $communicationFactory,
        FacturePresenceNonPayeRepository $facturePresenceNonPayeRepository
    ) {
        $this->factureRepository = $factureRepository;
        $this->factureFactory = $factureFactory;
        $this->presenceCalculator = $presenceCalculator;
        $this->facturePresenceRepository = $facturePresenceRepository;
        $this->presenceRepository = $presenceRepository;
        $this->accueilRepository = $accueilRepository;
        $this->accueilCalculator = $accueilCalculator;
        $this->tuteurRepository = $tuteurRepository;
        $this->communicationFactory = $communicationFactory;
        $this->facturePresenceNonPayeRepository = $facturePresenceNonPayeRepository;
    }

    public function newFacture(Tuteur $tuteur): FactureInterface
    {
        return $this->factureFactory->newInstance($tuteur);
    }

    /**
     * @param Facture $facture
     * @param array|int[] $presencesId
     * @param array|int[] $accueilsId
     * @return Facture
     */
    public function handleManually(FactureInterface $facture, array $presencesId, array $accueilsId): Facture
    {
        $presences = $this->presenceRepository->findBy(['id' => $presencesId]);
        $accueils = $this->accueilRepository->findBy(['id' => $accueilsId]);

        $this->finish($facture, $presences, $accueils);
        $this->flush();
        $facture->setCommunication($this->communicationFactory->generateForPresence($facture));
        $this->flush();

        return $facture;
    }

    public function generateByMonthForTuteur(Tuteur $tuteur, string $month): ?FactureInterface
    {
        [$month, $year] = explode('-', $month);
        $date = Carbon::createFromDate($year, $month, 01);

        $facture = $this->handleByTuteur($tuteur, $date);
        if ($facture !== null) {
            $this->flush();
            $facture->setCommunication($this->communicationFactory->generateForPresence($facture));
            $this->flush();
        }

        return $facture;
    }

    public function generateByMonthForEveryone(string $monthSelected): array
    {
        [$month, $year] = explode('-', $monthSelected);
        $date = Carbon::createFromDate($year, $month, 01);
        $factures = [];

        $tuteurs = $this->tuteurRepository->findAllOrderByNom();
        foreach ($tuteurs as $tuteur) {
            if (($facture = $this->handleByTuteur($tuteur, $date)) !== null) {
                $factures[] = $facture;
            }
        }

        $this->flush();
        foreach ($factures as $facture) {
            $facture->setCommunication($this->communicationFactory->generateForPresence($facture));
        }
        $this->flush();

        return $factures;
    }

    private function handleByTuteur(Tuteur $tuteur, CarbonInterface $date): ?Facture
    {
        $facture = $this->newFacture($tuteur);
        $facture->setMois($date->format('m-Y'));

        $presences = $this->facturePresenceNonPayeRepository->findPresencesNonPayes($tuteur, $date->toDateTime());
        $accueils = $this->facturePresenceNonPayeRepository->findAccueilsNonPayes($tuteur, $date->toDateTime());

        if ($presences === [] && $accueils === []) {
            return null;
        }

        $this->finish($facture, $presences, $accueils);

        return $facture;
    }

    public function isBilled(int $presenceId, string $type): bool
    {
        return (bool) $this->facturePresenceRepository->findByIdAndType($presenceId, $type);
    }

    public function isSended(int $presenceId, string $type): bool
    {
        if (($facturePresence = $this->facturePresenceRepository->findByIdAndType($presenceId, $type)) !== null) {
            return $facturePresence->getFacture()->getEnvoyeLe() != null;
        }

        return false;
    }

    /**
     * @param Facture $facture
     * @param array|Presence[] $presences
     * @param array|Accueil[] $accueils
     * @return Facture
     */
    private function finish(Facture $facture, array $presences, array $accueils): Facture
    {
        $this->attachPresences($facture, $presences);
        $this->attachAccueils($facture, $accueils);
        $this->attachRetard($facture, $accueils);
        $this->factureFactory->setEcoles($facture);

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
            $facturePresence = new FacturePresence($facture, $presence->getId(), FactureInterface::OBJECT_PRESENCE);
            $this->registerDataOnFacturePresence($facture, $presence, $facturePresence);
            $facturePresence->setCoutCalculated($this->presenceCalculator->calculate($presence));
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
            $facturePresence = new FacturePresence($facture, $accueil->getId(), FactureInterface::OBJECT_ACCUEIL);
            $facturePresence->setPresenceDate($accueil->getDateJour());
            $facturePresence->setHeure($accueil->getHeure());
            $facturePresence->setDuree($accueil->getDuree());
            $enfant = $accueil->getEnfant();
            if (($ecole = $enfant->getEcole()) !== null) {
                $facture->ecolesListing[$ecole->getId()] = $ecole;
            }
            $facturePresence->setNom($enfant->getNom());
            $facturePresence->setPrenom($enfant->getPrenom());
            $facturePresence->setCoutBrut($this->accueilCalculator->getPrix($accueil));
            $facturePresence->setCoutCalculated($this->accueilCalculator->calculate($accueil));
            $this->facturePresenceRepository->persist($facturePresence);
            $facture->addFacturePresence($facturePresence);
        }
    }

    public function registerDataOnFacturePresence(
        FactureInterface $facture,
        PresenceInterface $presence,
        FacturePresence $facturePresence
    ): void {
        $facturePresence->setPedagogique($presence->getJour()->isPedagogique());
        $facturePresence->setPresenceDate($presence->getJour()->getDateJour());
        $enfant = $presence->getEnfant();
        if (($ecole = $enfant->getEcole()) !== null) {
            $facture->ecolesListing[$ecole->getId()] = $ecole;
        }
        $facturePresence->setNom($enfant->getNom());
        $facturePresence->setPrenom($enfant->getPrenom());
        $ordre = $this->presenceCalculator->getOrdreOnPresence($presence);
        $facturePresence->setOrdre($ordre);
        $facturePresence->setAbsent($presence->getAbsent());
        $facturePresence->setCoutBrut($this->presenceCalculator->getPrixByOrdre($presence, $ordre));
    }

    private function flush(): void
    {
        $this->factureRepository->flush();
        $this->facturePresenceRepository->flush();
    }

    /**
     * @param array|Accueil[] $accueils
     */
    private function attachRetard(Facture $facture, array $accueils): void
    {
        $retards = [];
        $total = 0;
        foreach ($accueils as $accueil) {
            if ($accueil->getHeureRetard() !== null) {
                $total += $this->accueilCalculator->calculateRetard($accueil);
                $retards[] = $accueil->getDateJour()->format('d-m');
            }
        }
        if ($total > 0) {
            $complement = new FactureComplement($facture);
            $complement->setDateLe(new DateTime());
            $complement->setForfait($total);
            $complement->setNom('Retard pour les accueils: ' . implode(', ', $retards));
            $facture->addFactureComplement($complement);
            $this->factureRepository->persist($complement);
        }
    }
}
