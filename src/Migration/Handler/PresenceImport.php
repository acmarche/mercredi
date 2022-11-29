<?php

namespace AcMarche\Mercredi\Migration\Handler;

use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Entity\Presence\Presence;
use AcMarche\Mercredi\Migration\MercrediPdo;
use AcMarche\Mercredi\Migration\MigrationRepository;
use DateTime;
use Symfony\Component\Console\Style\SymfonyStyle;

class PresenceImport
{
    private SymfonyStyle $io;
    private MercrediPdo $pdo;

    public function __construct(
        private EnfantRepository $enfantRepository,
        private MigrationRepository $migrationRepository
    ) {
        $this->pdo = new MercrediPdo();
    }

    public function import(SymfonyStyle $io): void
    {
        $this->io = $io;
        $presences = $this->pdo->getAll('presence');
        foreach ($presences as $data) {
            $tuteur = $this->migrationRepository->getTuteur($data->tuteur_id);
            $enfant = $this->migrationRepository->getEnfant($data->enfant_id);
            $jour = $this->migrationRepository->getJour($data->jour_id);

            $presence = new Presence($tuteur, $enfant, $jour);
            $presence->setIdOld($data->id);
            if ($data->reduction_id > 0) {
                $reduction = $this->migrationRepository->getReduction($data->reduction_id);
                $presence->setReduction($reduction);
            }
            if ($data->paiement_id) {
                $paiement = $this->migrationRepository->getPaiement($data->paiement_id);
                $presence->setPaiement($paiement);
            }
            $ordre = $data->ordre ?? 0;
            $presence->setRemarque($data->remarques);
            $presence->setAbsent($data->absent);
            $presence->setOrdre($ordre);
            $user = $this->migrationRepository->getUser($data->user_add_id);
            $presence->setUserAdd($user->getUserIdentifier());
            $presence->generateUuid();
            $presence->setUpdatedAt(DateTime::createFromFormat('Y-m-d H:i:s', $data->updated));
            $presence->setCreatedAt(DateTime::createFromFormat('Y-m-d H:i:s', $data->created));
            $this->enfantRepository->persist($presence);
        }
        $this->enfantRepository->flush();
    }
}
