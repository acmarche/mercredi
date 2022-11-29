<?php

namespace AcMarche\Mercredi\Migration;

use AcMarche\Mercredi\Ecole\Repository\EcoleRepository;
use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Entity\Paiement;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Entity\Presence\Presence;
use AcMarche\Mercredi\Entity\Reduction;
use AcMarche\Mercredi\Entity\Sante\SanteFiche;
use AcMarche\Mercredi\Entity\Sante\SanteQuestion;
use AcMarche\Mercredi\Entity\Scolaire\AnneeScolaire;
use AcMarche\Mercredi\Entity\Scolaire\Ecole;
use AcMarche\Mercredi\Entity\Scolaire\GroupeScolaire;
use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Jour\Repository\JourRepository;
use AcMarche\Mercredi\Plaine\Repository\PlaineRepository;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Reduction\Repository\ReductionRepository;
use AcMarche\Mercredi\Sante\Repository\SanteFicheRepository;
use AcMarche\Mercredi\Sante\Repository\SanteQuestionRepository;
use AcMarche\Mercredi\Scolaire\Repository\AnneeScolaireRepository;
use AcMarche\Mercredi\Scolaire\Repository\GroupeScolaireRepository;
use AcMarche\Mercredi\Tuteur\Repository\TuteurRepository;
use AcMarche\Mercredi\User\Repository\UserRepository;
use DateTime;

class MigrationRepository
{
    private MercrediPdo $pdo;

    public function __construct(
        private UserRepository $userRepository,
        private EcoleRepository $ecoleRepository,
        private AnneeScolaireRepository $anneeScolaireRepository,
        private GroupeScolaireRepository $groupeScolaireRepository,
        private TuteurRepository $tuteurRepository,
        private EnfantRepository $enfantRepository,
        private SanteFicheRepository $santeFicheRepository,
        private SanteQuestionRepository $santeQuestionRepository,
        private JourRepository $jourRepository,
        private PlaineRepository $plaineRepository,
        private ReductionRepository $reductionRepository,
        private PresenceRepository $presenceRepository,
        private PaiementRepository $paiementRepository
    ) {
        $this->pdo = new MercrediPdo();
    }

    public function getUser(int $userId): ?User
    {
        $user = $this->pdo->getAllWhere('users', 'id = '.$userId, true);

        return $this->userRepository->findOneBy([
            'email' => $user->email,
        ]);
    }

    public function getUserStd(int $userId): \stdClass
    {
        return $this->pdo->getAllWhere('users', 'id = '.$userId, true);
    }

    public function getAnneeScolaire(string $name): ?AnneeScolaire
    {
        return $this->anneeScolaireRepository->findOneBy([
            'nom' => $name,
        ]);
    }

    public function getGroupeScolaire(string $name): ?GroupeScolaire
    {
        return $this->groupeScolaireRepository->findOneBy([
            'nom' => $name,
        ]);
    }

    public function getTuteur(int $tuteurId): ?Tuteur
    {
        $tuteurOld = $this->pdo->getAllWhere('tuteur', 'id = '.$tuteurId, true);
        $slug = preg_replace('#_#', '-', $tuteurOld->slugname);

        if (($tuteur = $this->tuteurRepository->findOneBy([
                'slug' => $slug,
            ])) === null) {
            $tuteur = $this->tuteurRepository->findOneBy(
                [
                    'nom' => $tuteurOld->nom,
                    'prenom' => $tuteurOld->prenom,
                    'code_postal' => $tuteurOld->code_postal,
                ]
            );
        }

        return $tuteur;
    }

    public function getTuteurOld(int $tuteurId): \stdClass
    {
        return $this->pdo->getAllWhere('tuteur', 'id = '.$tuteurId, true);
    }

    public function getEnfantStd(int $enfantId): \stdClass
    {
        return $this->pdo->getAllWhere('enfant', 'id = '.$enfantId, true);
    }

    public function getEnfant(int $enfantId): ?Enfant
    {
        $enfantOld = $this->pdo->getAllWhere('enfant', 'id = '.$enfantId, true);
        $slug = preg_replace('#_#', '-', $enfantOld->slugname);

        if (($enfant = $this->enfantRepository->findOneBy([
                'slug' => $slug,
            ])) === null) {
            $enfant = $this->enfantRepository->findOneBy(
                [
                    'nom' => $enfantOld->nom,
                    'prenom' => $enfantOld->prenom,
                    'birthday' => DateTime::createFromFormat('Y-m-d', $enfantOld->birthday),
                ]
            );
        }

        return $enfant;
    }

    public function getEcole(int $ecoleId): ?Ecole
    {
        $ecole = $this->pdo->getAllWhere('ecole', 'id = '.$ecoleId, true);

        return $this->ecoleRepository->findOneBy([
            'nom' => $ecole->nom,
        ]);
    }

    public function getSanteFiche(int $santeFicheId): ?SanteFiche
    {
        $santeFiche = $this->pdo->getAllWhere('sante_fiche', 'id = '.$santeFicheId, true);
        $enfant = $this->getEnfant($santeFiche->enfant_id);

        return $this->santeFicheRepository->findOneBy([
            'enfant' => $enfant,
        ]);
    }

    public function getSanteQuestion($questionId): ?SanteQuestion
    {
        $question = $this->pdo->getAllWhere('sante_question', 'id = '.$questionId, true);

        return $this->santeQuestionRepository->findOneBy([
            'nom' => $question->intitule,
        ]);
    }

    public function getJour(int $jourId): ?Jour
    {
        $jour = $this->pdo->getAllWhere('jour', 'id = '.$jourId, true);

        return $this->jourRepository->findOneBy(
            [
                'date_jour' => DateTime::createFromFormat('Y-m-d', $jour->date_jour),
            ]
        );
    }

    public function getJourPlaine(int $jourId): ?Jour
    {
        $jour = $this->pdo->getAllWhere('plaine_jours', 'id = '.$jourId, true);

        return $this->jourRepository->findOneBy(
            [
                'date_jour' => DateTime::createFromFormat('Y-m-d', $jour->date_jour),
            ]
        );
    }

    public function getPlaineEnfantStd(int $id): ?\stdClass
    {
        return $this->pdo->getAllWhere('plaine_enfant', 'id = '.$id, true);
    }

    public function getReduction(int $reductionId): ?Reduction
    {
        $reduction = $this->pdo->getAllWhere('reduction', 'id = '.$reductionId, true);

        return $this->reductionRepository->findOneBy([
            'nom' => $reduction->nom,
        ]);
    }

    public function getPaiement(int $paiementId): ?Paiement
    {
        $paiement = $this->pdo->getAllWhere('paiement', 'id = '.$paiementId, true);

        return $this->paiementRepository->findOneBy(
            [
                'tuteur' => $paiement->tuteur_id,
                'date_paiement' => DateTime::createFromFormat('Y-m-d', $paiement->date_paiement),
            ]
        );
    }

    public function getPlaine(int $plaineId): ?Plaine
    {
        $plaine = $this->pdo->getAllWhere('plaine', 'id = '.$plaineId, true);

        return $this->plaineRepository->findOneBy([
            'nom' => $plaine->intitule,
        ]);
    }

    public function getPresence(int $tuteurId, Enfant $enfant, Jour $jour): Presence
    {
        $tuteur = $this->getTuteur($tuteurId);

        $presence = $this->presenceRepository->findOneBy([
            'enfant' => $enfant,
            'tuteur' => $tuteur,
            'jour' => $jour,
        ]);
        if (!$presence instanceof Presence) {
            dd(
                $enfant->getId().' '.$enfant->getNom().' '.$enfant->getPrenom().' '.$tuteur->getId(
                ).' '.' '.$tuteur->getNom().' '.$tuteur->getPrenom().' '.$jour->getDateJour()->format(
                    'Y-m-d'
                ).' '.$jour->getId()
            );
        }

        return $presence;
    }
}
