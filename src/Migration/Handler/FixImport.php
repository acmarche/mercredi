<?php

namespace AcMarche\Mercredi\Migration\Handler;

use AcMarche\Mercredi\Entity\Paiement;
use AcMarche\Mercredi\Entity\Presence\Presence;
use AcMarche\Mercredi\Migration\MercrediPdo;
use AcMarche\Mercredi\Migration\MigrationRepository;
use AcMarche\Mercredi\Migration\PaiementRepository;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Tuteur\Repository\TuteurRepository;
use Symfony\Component\Console\Style\SymfonyStyle;

class FixImport
{
    private SymfonyStyle $io;
    private MercrediPdo $pdo;

    public function __construct(
        private PaiementRepository $paiementRepository,
        private MigrationRepository $migrationRepository,
        private TuteurRepository $tuteurRepository,
        private PresenceRepository $presenceRepository
    ) {
        $this->pdo = new MercrediPdo();
    }

    public function import(SymfonyStyle $io): void
    {
        $presencesOld = $this->pdo->getAll('presence');

        foreach ($presencesOld as $presenceOld) {
            $presencenew = $this->presenceRepository->findOneBy(['idOld' => $presenceOld->id]);
            if (!$presencenew instanceof Presence) {
                dump($presenceOld);
                break;
            }
        }
    }

    public function setIdPaiements(SymfonyStyle $io)
    {
        $paiementsOld = $this->pdo->getAll('paiement');

        foreach ($paiementsOld as $paiementOld) {
            $paiementNew = $this->getNewPaiementFromPaiementOld($paiementOld);
            //    $userAddOld = $this->migrationRepository->getUserStd($paiementOld->user_add_id);
            $tuteurOld = $this->migrationRepository->getTuteurOld($paiementOld->tuteur_id);
            $tuteurNew = $this->tuteurRepository->findOneBy(['idOld' => $paiementOld->tuteur_id]);

            if ($tuteurNew->getNom() != $tuteurOld->nom) {
                dump($paiementOld);
                dump($tuteurOld);
            }

            $paiementNew->setIdOld($paiementOld->id);
        }
        $this->tuteurRepository->flush();
    }

    private function getNewPaiementFromPaiementOld(\stdClass $paiementOld): ?Paiement
    {
        $datePaiement = \DateTime::createFromFormat('Y-m-d', $paiementOld->date_paiement);
        $userAddOld = $this->migrationRepository->getUserStd($paiementOld->user_add_id);

        $args = [
            'date_paiement' => $datePaiement,
            'montant' => $paiementOld->montant,
            'type_paiement' => $paiementOld->type_paiement,
            'userAdd' => $userAddOld->email,
        ];

        return $this->paiementRepository->findOneBy($args);
    }

}