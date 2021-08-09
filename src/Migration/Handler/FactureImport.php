<?php


namespace AcMarche\Mercredi\Migration\Handler;

use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Entity\Facture\FacturePresence;
use AcMarche\Mercredi\Entity\Presence\Presence;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Facture\Factory\CommunicationFactory;
use AcMarche\Mercredi\Facture\FactureInterface;
use AcMarche\Mercredi\Facture\Handler\FactureHandler;
use AcMarche\Mercredi\Migration\MercrediPdo;
use AcMarche\Mercredi\Migration\MigrationRepository;
use AcMarche\Mercredi\Presence\Calculator\PresenceCalculatorInterface;
use AcMarche\Mercredi\Tuteur\Repository\TuteurRepository;
use Symfony\Component\Console\Style\SymfonyStyle;

class FactureImport
{
    private SymfonyStyle $io;
    private TuteurRepository $tuteurRepository;
    private MigrationRepository $migrationRepository;
    private FactureHandler $factureHandler;
    private CommunicationFactory $communicationFactory;
    private PresenceCalculatorInterface $presenceCalculator;

    public function __construct(
        TuteurRepository $tuteurRepository,
        MigrationRepository $migrationRepository,
        FactureHandler $factureHandler,
        CommunicationFactory $communicationFactory,
        PresenceCalculatorInterface $presenceCalculator
    ) {
        $this->tuteurRepository = $tuteurRepository;
        $this->migrationRepository = $migrationRepository;
        $this->factureHandler = $factureHandler;
        $this->communicationFactory = $communicationFactory;
        $this->presenceCalculator = $presenceCalculator;
    }

    public function import(SymfonyStyle $io)
    {
        $this->io = $io;
        $pdo = new MercrediPdo();
        $paiements = $pdo->getAll('paiement');
        foreach ($paiements as $paiement) {
            dump($paiement);
            $io->writeln($paiement->date_paiement);
            $tuteur = $this->migrationRepository->getTuteur((int)$paiement->tuteur_id);
            $facture = $this->createFacture($paiement, $tuteur);
            $type = FactureInterface::OBJECT_PRESENCE;
            if ($paiement->type_paiement == 'Plaine') {
                $type = FactureInterface::OBJECT_PLAINE;
            }
            foreach ($pdo->getAllWhere('presence', 'paiement_id = '.$paiement->id, false) as $row) {
                $enfant = $this->migrationRepository->getEnfant($row->enfant_id);
                $presence = $this->migrationRepository->getPresence($row->tuteur_id, $row->enfant_id, $row->jour_id);
                $this->attachPresence($facture, $presence, $type);
            }
            $facture->setEcoles($enfant->getEcole()->getNom());
        }

        // $this->tuteurRepository->flush();
    }

    private function createFacture($paiement, Tuteur $tuteur): Facture
    {
        $facture = $this->factureHandler->newInstance($tuteur);
        if ($paiement->date_paiement) {
            $facture->setPayeLe(\DateTime::createFromFormat('Y-m-d', $paiement->date_paiement));
        }
        $facture->setCreatedAt(\DateTime::createFromFormat('Y-m-d H:i:s', $paiement->created));
        $facture->setUpdatedAt(\DateTime::createFromFormat('Y-m-d H:i:s', $paiement->updated));
        $facture->setMontantObsolete($paiement->montant);
        $facture->setClotureObsolete($paiement->cloture);
        $facture->setMois(\DateTime::createFromFormat('Y-m-d', $paiement->date_paiement)->format('m-Y'));
        $facture->setRemarque(
            'type et mode de paiement: '.$paiement->type_paiement.' '.$paiement->mode_paiement.' ordre: '.$paiement->ordre
        );
        $user = $this->migrationRepository->getUser($paiement->user_add_id);
        $facture->setUserAdd($user);
        $facture->setCommunication($this->communicationFactory->generate($facture));
        $this->tuteurRepository->persist($facture);

        return $facture;
    }

    private function attachPresence(Facture $facture, Presence $presence, string $type)
    {
        $jour = $presence->getJour();
        $facturePresence = new FacturePresence($facture, $presence->getId(), $type);
        $facturePresence->setPedagogique($jour->isPedagogique());
        $facturePresence->setPresenceDate($jour->getDateJour());
        $enfant = $presence->getEnfant();
        $facturePresence->setNom($enfant->getNom());
        $facturePresence->setPrenom($enfant->getPrenom());
        $facturePresence->setCout($this->presenceCalculator->calculate($presence));
        $this->tuteurRepository->persist($facturePresence);
        $facture->addFacturePresence($facturePresence);
    }

}
