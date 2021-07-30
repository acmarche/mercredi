<?php

namespace AcMarche\Mercredi\Migration\Handler;

use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Entity\Presence;
use AcMarche\Mercredi\Migration\MercrediPdo;
use AcMarche\Mercredi\Migration\MigrationRepository;
use Symfony\Component\Console\Style\SymfonyStyle;

class PresenceImport
{
    private SymfonyStyle $io;
    private EnfantRepository $enfantRepository;
    private MigrationRepository $migrationRepository;
    private MercrediPdo $pdo;

    public function __construct(
        EnfantRepository $enfantRepository,
        MigrationRepository $migrationRepository
    ) {
        $this->enfantRepository = $enfantRepository;
        $this->migrationRepository = $migrationRepository;
        $this->pdo = new MercrediPdo();
    }

    public function import(SymfonyStyle $io)
    {
        $this->io = $io;
        $presences = $this->pdo->getAll('presence');
        foreach ($presences as $data) {

            $tuteur = $this->migrationRepository->getTuteur($data->tuteur_id);
            $enfant = $this->migrationRepository->getEnfant($data->enfant_id);
            $jour = $this->migrationRepository->getJour($data->jour_id);

            $presence = new Presence($tuteur, $enfant, $jour);
            if ($data->reduction_id > 0) {
                $reduction = $this->migrationRepository->getReduction($data->reduction_id);
                $presence->setReduction($reduction);
            }
            $ordre = $data->ordre ?? 0;
            $presence->setRemarque($data->remarques);
            $presence->setAbsent($data->absent);
            $presence->setOrdre($ordre);
            $user = $this->migrationRepository->getUser($data->user_add_id);
            $presence->setUserAdd($user->getUserIdentifier());
            $presence->generateUuid();
            $presence->setUpdatedAt(\DateTime::createFromFormat('Y-m-d H:i:s', $data->updated));
            $presence->setCreatedAt(\DateTime::createFromFormat('Y-m-d H:i:s', $data->created));
            $this->enfantRepository->persist($presence);
        }
        $this->enfantRepository->flush();
    }

}
