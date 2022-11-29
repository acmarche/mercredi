<?php

namespace AcMarche\Mercredi\Migration\Handler;

use AcMarche\Mercredi\Entity\Paiement;
use AcMarche\Mercredi\Migration\MercrediPdo;
use AcMarche\Mercredi\Migration\MigrationRepository;
use AcMarche\Mercredi\Migration\PaiementRepository;
use DateTime;
use Symfony\Component\Console\Style\SymfonyStyle;

class PaiementImport
{
    private SymfonyStyle $io;
    private MercrediPdo $pdo;

    public function __construct(
        private PaiementRepository $paiementRepository,
        private MigrationRepository $migrationRepository
    ) {
        $this->pdo = new MercrediPdo();
    }

    public function import(SymfonyStyle $io): void
    {
        $this->io = $io;
        $paiements = $this->pdo->getAll('paiement');
        $io->writeln(count($paiements).' counts ');
        foreach ($paiements as $data) {
            $tuteur = $this->migrationRepository->getTuteur($data->tuteur_id);
            $enfant = null;
            if ($data->enfant_id) {
                $enfant = $this->migrationRepository->getEnfant($data->enfant_id);
            }

            $this->io->writeln($data->date_paiement);
            $paiement = new Paiement();
            $paiement->setTuteur($tuteur);
            if (null !== $enfant) {
                $paiement->setEnfant($enfant);
            }
            if ($datePaiement = DateTime::createFromFormat('Y-m-d', $data->date_paiement)) {
                $paiement->setDatePaiement($datePaiement);
            }
            $paiement->setMontant($data->montant);
            $paiement->setOrdre($data->ordre);
            $paiement->setTypePaiement($data->type_paiement);
            $paiement->setModePaiement($data->mode_paiement);
            $paiement->setRemarques($data->remarques);
            $user = $this->migrationRepository->getUser($data->user_add_id);
            $paiement->setUserAdd($user->getUserIdentifier());
            $paiement->setUpdatedAt(DateTime::createFromFormat('Y-m-d H:i:s', $data->updated));
            $paiement->setCreatedAt(DateTime::createFromFormat('Y-m-d H:i:s', $data->created));
            $this->paiementRepository->persist($paiement);
        }
        $this->paiementRepository->flush();
    }
}
