<?php


namespace AcMarche\Mercredi\Migration;


use AcMarche\Mercredi\Ecole\Repository\EcoleRepository;
use AcMarche\Mercredi\Enfant\Repository\EnfantRepository;
use AcMarche\Mercredi\Entity\AnneeScolaire;
use AcMarche\Mercredi\Entity\Ecole;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\GroupeScolaire;
use AcMarche\Mercredi\Entity\Sante\SanteFiche;
use AcMarche\Mercredi\Entity\Sante\SanteQuestion;
use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Entity\Tuteur;
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

    public function __construct(
        UserRepository $userRepository,
        EcoleRepository $ecoleRepository,
        AnneeScolaireRepository $anneeScolaireRepository,
        GroupeScolaireRepository $groupeScolaireRepository,
        TuteurRepository $tuteurRepository,
        EnfantRepository $enfantRepository,
        SanteFicheRepository $santeFicheRepository,
        SanteQuestionRepository $santeQuestionRepository
    ) {
        $this->userRepository = $userRepository;
        $this->ecoleRepository = $ecoleRepository;
        $this->anneeScolaireRepository = $anneeScolaireRepository;
        $this->groupeScolaireRepository = $groupeScolaireRepository;
        $this->pdo = new MercrediPdo();
        $this->tuteurRepository = $tuteurRepository;
        $this->enfantRepository = $enfantRepository;
        $this->santeFicheRepository = $santeFicheRepository;
        $this->santeQuestionRepository = $santeQuestionRepository;
    }

    public function getUser(int $userId): User
    {
        $user = $this->pdo->getAllWhere('users', 'id = '.$userId, true);

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
        $tuteurOld = $this->pdo->getAllWhere('tuteur', 'id = '.$tuteurId, true);
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
        $enfantOld = $this->pdo->getAllWhere('enfant', 'id = '.$enfantId, true);
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
        $ecole = $this->pdo->getAllWhere('ecole', 'id = '.$ecoleId, true);

        return $this->ecoleRepository->findOneBy(['nom' => $ecole->nom]);
    }

    public function getSanteFiche(int $santeFicheId): SanteFiche
    {
        $santeFiche = $this->pdo->getAllWhere('sante_fiche', 'id = '.$santeFicheId, true);
        $enfant = $this->getEnfant($santeFiche->enfant_id);

        return $this->santeFicheRepository->findOneBy(['enfant' => $enfant]);
    }

    public function getSanteQuestion($questionId): SanteQuestion
    {
        $question = $this->pdo->getAllWhere('sante_question', 'id = '.$questionId, true);

        return $this->santeQuestionRepository->findOneBy(['nom' => $question->intitule]);
    }
}
