<?php

namespace AcMarche\Mercredi\Migration\Handler;

use AcMarche\Mercredi\Entity\Presence\Presence;
use AcMarche\Mercredi\Migration\MercrediPdo;
use AcMarche\Mercredi\Migration\MigrationRepository;
use AcMarche\Mercredi\Tuteur\Repository\TuteurRepository;
use DateTime;
use Symfony\Component\Console\Style\SymfonyStyle;

class PlainePresenceImport
{
    private SymfonyStyle $io;
    private MercrediPdo $pdo;

    public function __construct(
        private TuteurRepository $tuteurRepository,
        private MigrationRepository $migrationRepository
    ) {
        $this->pdo = new MercrediPdo();
    }

    public function import(SymfonyStyle $io): void
    {
        $this->io = $io;
        $plaine_enfants = $this->pdo->getAll('plaine_enfant');
        foreach ($plaine_enfants as $data) {
            $enfant = $this->migrationRepository->getEnfant($data->enfant_id);
            $plaine = $this->migrationRepository->getPlaine($data->plaine_id);
            $plaine_presences = $this->pdo->getAllWhere('plaine_presences', 'plaine_enfant_id = '.$data->id, false);
            foreach ($plaine_presences as $plainePresence) {
                $jour = $this->migrationRepository->getJourPlaine($plainePresence->jour_id);
                if (!$plainePresence->tuteur_id) {
                    $relations = $this->pdo->getAllWhere('enfant_tuteur', 'enfant_id = '.$data->enfant_id, false);
                    $count = is_countable($relations) ? \count($relations) : 0;
                    if ($count > 0) {
                        $tuteur = $this->migrationRepository->getTuteur($relations[0]->tuteur_id);
                    }
                    if ($count > 1) {
                        $io->error(
                            $plaine->getNom().';'.$enfant.';'.$plainePresence->id
                        );
                    }
                } else {
                    $tuteur = $this->migrationRepository->getTuteur($plainePresence->tuteur_id);
                }
                $presence = new Presence($tuteur, $enfant, $jour);
                $presence->setIdOld($plainePresence->id);
                $ordre = $plainePresence->ordre ?? 0;
                $presence->setRemarque($plainePresence->remarques);
                $presence->setAbsent($plainePresence->absent);
                $presence->setOrdre($ordre);
                if ($plainePresence->paiement_id) {
                    $paiement = $this->migrationRepository->getPaiement($plainePresence->paiement_id);
                    $presence->setPaiement($paiement);
                }
                $user = $this->migrationRepository->getUser($plainePresence->user_add_id);
                $presence->setUserAdd($user->getUserIdentifier());
                $presence->generateUuid();
                $presence->setUpdatedAt(DateTime::createFromFormat('Y-m-d H:i:s', $plainePresence->updated));
                $presence->setCreatedAt(DateTime::createFromFormat('Y-m-d H:i:s', $plainePresence->created));
                $this->tuteurRepository->persist($presence);
            }
        }
        $this->tuteurRepository->flush();
    }
}
