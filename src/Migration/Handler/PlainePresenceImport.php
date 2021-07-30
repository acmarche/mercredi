<?php

namespace AcMarche\Mercredi\Migration\Handler;

use AcMarche\Mercredi\Entity\Presence;
use AcMarche\Mercredi\Migration\MercrediPdo;
use AcMarche\Mercredi\Migration\MigrationRepository;
use AcMarche\Mercredi\Tuteur\Repository\TuteurRepository;
use Symfony\Component\Console\Style\SymfonyStyle;

class PlainePresenceImport
{
    private SymfonyStyle $io;
    private TuteurRepository $tuteurRepository;
    private MigrationRepository $migrationRepository;

    public function __construct(
        TuteurRepository $tuteurRepository,
        MigrationRepository $migrationRepository
    ) {
        $this->tuteurRepository = $tuteurRepository;
        $this->migrationRepository = $migrationRepository;
    }

    public function import(SymfonyStyle $io)
    {
        $this->io = $io;
        $pdo = new MercrediPdo();
        $plaine_enfants = $pdo->getAll('plaine_enfant');
        foreach ($plaine_enfants as $data) {
            $enfant = $this->migrationRepository->getEnfant($data->enfant_id);
            $plaine_enfants = $pdo->getAllWhere('plaine_presences', 'plaine_enfant_id = '.$data->id, false);
            foreach ($plaine_enfants as $plaineEnfant) {
                $tuteur = $plaineEnfant->tuteur_id;
                $jour = $this->migrationRepository->getJour($plaineEnfant->jour_id);
                $presence = new Presence($tuteur, $enfant, $jour);
                $ordre = $data->ordre ?? 0;
                $presence->setRemarque($data->remarques);
                $presence->setAbsent($data->absent);
                $presence->setOrdre($ordre);
                $user = $this->migrationRepository->getUser($data->user_add_id);
                $presence->setUserAdd($user->getUserIdentifier());
                $presence->generateUuid();
                $presence->setUpdatedAt(\DateTime::createFromFormat('Y-m-d H:i:s', $data->updated));
                $presence->setCreatedAt(\DateTime::createFromFormat('Y-m-d H:i:s', $data->created));
                $this->tuteurRepository->persist($presence);
            }
        }
        //$this->tuteurRepository->flush();
    }
}
