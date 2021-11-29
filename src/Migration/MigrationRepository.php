<?php


namespace AcMarche\Mercredi\Migration;

use AcMarche\Mercredi\Ecole\Repository\EcoleRepository;
use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Jour;
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

class MigrationRepository
{
    private UserRepository $userRepository;
    private EcoleRepository $ecoleRepository;
    private AnneeScolaireRepository $anneeScolaireRepository;
    private GroupeScolaireRepository $groupeScolaireRepository;
    private MercrediPdo $pdo;
    private TuteurRepository $tuteurRepository;
    private EnfantRepository $enfantRepository;
    private SanteFicheRepository $santeFicheRepository;
    private SanteQuestionRepository $santeQuestionRepository;
    private JourRepository $jourRepository;
    private ReductionRepository $reductionRepository;
    private PlaineRepository $plaineRepository;
    private PresenceRepository $presenceRepository;

    public function __construct(
        UserRepository $userRepository,
        EcoleRepository $ecoleRepository,
        AnneeScolaireRepository $anneeScolaireRepository,
        GroupeScolaireRepository $groupeScolaireRepository,
        TuteurRepository $tuteurRepository,
        EnfantRepository $enfantRepository,
        SanteFicheRepository $santeFicheRepository,
        SanteQuestionRepository $santeQuestionRepository,
        JourRepository $jourRepository,
        PlaineRepository $plaineRepository,
        ReductionRepository $reductionRepository,
        PresenceRepository $presenceRepository
    ) {
        $this->userRepository = $userRepository;
        $this->ecoleRepository = $ecoleRepository;
        $this->anneeScolaireRepository = $anneeScolaireRepository;
        $this->groupeScolaireRepository = $groupeScolaireRepository;
        $this->tuteurRepository = $tuteurRepository;
        $this->enfantRepository = $enfantRepository;
        $this->santeFicheRepository = $santeFicheRepository;
        $this->santeQuestionRepository = $santeQuestionRepository;
        $this->jourRepository = $jourRepository;
        $this->reductionRepository = $reductionRepository;
        $this->plaineRepository = $plaineRepository;
        $this->presenceRepository = $presenceRepository;
        $this->pdo = new MercrediPdo();
    }

    public function getUser(int $userId): User
    {
        $user = $this->pdo->getAllWhere('users', 'id = ' . $userId, true);

        return $this->userRepository->findOneBy(['email' => $user->email]);
    }

    public function getAnneeScolaire(string $name): AnneeScolaire
    {
        return $this->anneeScolaireRepository->findOneBy(['nom' => $name]);
    }

    public function getGroupeScolaire(string $name): GroupeScolaire
    {
        return $this->groupeScolaireRepository->findOneBy(['nom' => $name]);
    }

    public function getTuteur(int $tuteurId): Tuteur
    {
        $tuteurOld = $this->pdo->getAllWhere('tuteur', 'id = ' . $tuteurId, true);
        $slug = preg_replace("#_#", '-', $tuteurOld->slugname);

        if (!$tuteur = $this->tuteurRepository->findOneBy(['slug' => $slug])) {
            $tuteur = $this->tuteurRepository->findOneBy(
                ['nom' => $tuteurOld->nom, 'prenom' => $tuteurOld->prenom, 'code_postal' => $tuteurOld->code_postal]
            );
        }

        return $tuteur;
    }

    public function getEnfant(int $enfantId): Enfant
    {
        $enfantOld = $this->pdo->getAllWhere('enfant', 'id = ' . $enfantId, true);
        $slug = preg_replace("#_#", '-', $enfantOld->slugname);

        if (!$enfant = $this->enfantRepository->findOneBy(['slug' => $slug])) {
            $enfant = $this->enfantRepository->findOneBy(
                [
                    'nom' => $enfantOld->nom,
                    'prenom' => $enfantOld->prenom,
                    'birthday' => \DateTime::createFromFormat('Y-m-d', $enfantOld->birthday),
                ]
            );
        }

        return $enfant;
    }

    public function getEcole(int $ecoleId): Ecole
    {
        $ecole = $this->pdo->getAllWhere('ecole', 'id = ' . $ecoleId, true);

        return $this->ecoleRepository->findOneBy(['nom' => $ecole->nom]);
    }

    public function getSanteFiche(int $santeFicheId): SanteFiche
    {
        $santeFiche = $this->pdo->getAllWhere('sante_fiche', 'id = ' . $santeFicheId, true);
        $enfant = $this->getEnfant($santeFiche->enfant_id);

        return $this->santeFicheRepository->findOneBy(['enfant' => $enfant]);
    }

    public function getSanteQuestion($questionId): SanteQuestion
    {
        $question = $this->pdo->getAllWhere('sante_question', 'id = ' . $questionId, true);

        return $this->santeQuestionRepository->findOneBy(['nom' => $question->intitule]);
    }

    public function getJour(int $jourId): Jour
    {
        $jour = $this->pdo->getAllWhere('jour', 'id = ' . $jourId, true);

        return $this->jourRepository->findOneBy(
            ['date_jour' => \DateTime::createFromFormat('Y-m-d', $jour->date_jour)]
        );
    }

    public function getJourPlaine(int $jourId): Jour
    {
        $jour = $this->pdo->getAllWhere('plaine_jours', 'id = ' . $jourId, true);

        return $this->jourRepository->findOneBy(
            ['date_jour' => \DateTime::createFromFormat('Y-m-d', $jour->date_jour)]
        );
    }

    public function getReduction(int $reductionId): Reduction
    {
        $reduction = $this->pdo->getAllWhere('reduction', 'id = ' . $reductionId, true);

        return $this->reductionRepository->findOneBy(['nom' => $reduction->nom]);
    }

    public function getPlaine(int $plaineId): Plaine
    {
        $plaine = $this->pdo->getAllWhere('plaine', 'id = ' . $plaineId, true);

        return $this->plaineRepository->findOneBy(['nom' => $plaine->intitule]);
    }

    public function getPresence(int $tuteurId, Enfant $enfant, Jour $jour): Presence
    {
        $tuteur = $this->getTuteur($tuteurId);

        $presence = $this->presenceRepository->findOneBy(['enfant' => $enfant, 'tuteur' => $tuteur, 'jour' => $jour]);
        if (!$presence) {
            dd(
                $enfant->getId() . ' ' . $enfant->getNom() . ' ' . $enfant->getPrenom() . ' ' . $tuteur->getId(
                ) . ' ' . ' ' . $tuteur->getNom() . ' ' . $tuteur->getPrenom() . ' ' . $jour->getDateJour()->format(
                    'Y-m-d'
                ) . ' ' . $jour->getId()
            );
        }

        return $presence;
    }
}
