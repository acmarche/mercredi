<?php

namespace AcMarche\Mercredi\Migration\Handler;

use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Entity\Paiement;
use AcMarche\Mercredi\Entity\Presence\Presence;
use AcMarche\Mercredi\Migration\MercrediPdo;
use AcMarche\Mercredi\Migration\MigrationRepository;
use AcMarche\Mercredi\Migration\PaiementRepository;
use AcMarche\Mercredi\Plaine\Repository\PlaineRepository;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Tuteur\Repository\TuteurRepository;
use AcMarche\Mercredi\User\Repository\UserRepository;
use DateTime;
use Symfony\Component\Console\Style\SymfonyStyle;

class FixImport
{
    private SymfonyStyle $io;
    private MercrediPdo $pdo;

    public function __construct(
        private PaiementRepository $paiementRepository,
        private MigrationRepository $migrationRepository,
        private TuteurRepository $tuteurRepository,
        private PresenceRepository $presenceRepository,
        private EnfantRepository $enfantRepository,
        private UserRepository $userRepository,
        private PlaineRepository $plaineRepository
    ) {
        $this->pdo = new MercrediPdo();
    }

    public function import(SymfonyStyle $io): void
    {
        $this->io = $io;
        $paiements = $this->pdo->getAll('paiement');
        $io->writeln(count($paiements).' counts ');
        foreach ($paiements as $data) {
            $tuteur = $this->tuteurRepository->findOneBy(['idOld' => $data->tuteur_id]);
            if (!$tuteur) {
                $io->writeln('tuteur not found');
                dump($data);
                break;
            }
            $enfant = null;
            if ($data->enfant_id) {
                $enfant = $this->enfantRepository->findOneBy(['idOld' => $data->enfant_id]);
                if (!$enfant) {
                    $io->writeln('enfant not found');
                    dump($data);
                    break;
                }
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
            $paiement->setIdOld($data->id);
            $paiement->setMontant($data->montant);
            $paiement->setOrdre($data->ordre);
            $paiement->setTypePaiement($data->type_paiement);
            $paiement->setModePaiement($data->mode_paiement);
            $paiement->setRemarques($data->remarques);
            $userOld = $this->migrationRepository->getUserStd($data->user_add_id);
            $user = $this->userRepository->findOneBy(['email' => $userOld->email]);
            if (!$user) {
                $username = 'jean-philippe.adam@ac.marche.be';
            } else {
                $username = $user->getUserIdentifier();
            }
            $paiement->setUserAdd($username);
            $paiement->setUpdatedAt(DateTime::createFromFormat('Y-m-d H:i:s', $data->updated));
            $paiement->setCreatedAt(DateTime::createFromFormat('Y-m-d H:i:s', $data->created));
            $this->paiementRepository->persist($paiement);
        }
        //       $this->paiementRepository->flush();
    }

    public function fixPaiement(SymfonyStyle $io): void
    {
        $presencesOld = $this->pdo->getAll('presence');
        $i = 0;

        foreach ($presencesOld as $presenceOld) {
            if ($presenceOld->paiement_id) {
                $presencenew = $this->presenceRepository->findOneBy(['idOld' => $presenceOld->id]);
                $paiementNew = $this->paiementRepository->findOneBy(['idOld' => $presenceOld->paiement_id]);
                //$enfantNew = $this->enfantRepository->findOneBy(['idOld' => $presenceOld->enfant_id]);
                $enfantOld = $this->migrationRepository->getEnfantStd($presenceOld->enfant_id);
                if (!$paiementNew) {
                    dump($presenceOld);
                    dump($presencenew->getId());
                    $io->write($enfantOld->nom.' '.$enfantOld->prenom.' ');
                    $io->writeln($presencenew->getEnfant()->getNom().' '.$presencenew->getEnfant()->getPrenom());
                }
                $presencenew->setPaiement($paiementNew);
                $i++;
            }
        }
        $this->presenceRepository->flush();
        dump($i);
    }

    public function fixPaiementPlaine(SymfonyStyle $io): void
    {
        $presencesOld = $this->pdo->getAll('plaine_presences');
        $i = 0;

        foreach ($presencesOld as $presenceOld) {
            if ($presenceOld->paiement_id) {
                $presencenew = $this->findPresencePlaineOneBy($presenceOld);
                if (!$presencenew) {
                    dump($presenceOld);
                    break;
                }
                $paiementNew = $this->paiementRepository->findOneBy(['idOld' => $presenceOld->paiement_id]);
                if (!$paiementNew) {
                    dump($presenceOld);
                    dump($presencenew->getId());
                }
                $presencenew->setPaiement($paiementNew);
            }
        }
        $this->presenceRepository->flush();
    }

    public function importPlaine(SymfonyStyle $io): void
    {
        $this->io = $io;
        $plaine_enfants = $this->pdo->getAll('plaine_enfant');
        foreach ($plaine_enfants as $data) {
            $enfant = $this->enfantRepository->findOneBy(['idOld' => $data->enfant_id]);
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

    private function findPresencePlaineOneBy(mixed $presenceOld): ?Presence
    {
        $tuteur = $this->tuteurRepository->findOneBy(['idOld' => $presenceOld->tuteur_id]);
        //$plaine = $this->migrationRepository->getPlaine($presenceOld->plaine_id);
        $jour = $this->migrationRepository->getJourPlaine($presenceOld->jour_id);
        $plaineEnfant = $this->migrationRepository->getPlaineEnfantStd($presenceOld->plaine_enfant_id);
        $enfant = $this->enfantRepository->findOneBy(['idOld' => $plaineEnfant->enfant_id]);
        // $plaine = $this->plaineRepository->findOneBy(['idOld' => $plaineEnfant->plaine_id]);

        $presence = $this->presenceRepository->findOneBy([
            'enfant' => $enfant,
            'tuteur' => $tuteur,
            'jour' => $jour,
        ]);

        return $presence;
    }


}