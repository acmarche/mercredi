<?php


namespace AcMarche\Mercredi\Migration;


use AcMarche\Mercredi\Ecole\Repository\EcoleRepository;
use AcMarche\Mercredi\Entity\AnneeScolaire;
use AcMarche\Mercredi\Entity\GroupeScolaire;
use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Scolaire\Repository\AnneeScolaireRepository;
use AcMarche\Mercredi\Scolaire\Repository\GroupeScolaireRepository;
use AcMarche\Mercredi\User\Repository\UserRepository;

class MigrationRepository
{
    private UserRepository $userRepository;
    private EcoleRepository $ecoleRepository;
    private AnneeScolaireRepository $anneeScolaireRepository;
    private GroupeScolaireRepository $groupeScolaireRepository;

    public function __construct(
        UserRepository $userRepository,
        EcoleRepository $ecoleRepository,
        AnneeScolaireRepository $anneeScolaireRepository,
        GroupeScolaireRepository $groupeScolaireRepository
    ) {
        $this->userRepository = $userRepository;
        $this->ecoleRepository = $ecoleRepository;
        $this->anneeScolaireRepository = $anneeScolaireRepository;
        $this->groupeScolaireRepository = $groupeScolaireRepository;
    }

    public function getUser(int $userId): User
    {
        $this->pdo = new MercrediPdo();
        $user = $this->pdo->getAllWhere('users', 'id = '.$userId, true);

        return $this->userRepository->findOneBy(['email' => $user->email]);
    }

    public function getAnneeScolaire(int $userId): AnneeScolaire
    {
        return $this->anneeScolaireRepository->find($userId);
    }

    public function getGroupeScolaire(int $userId): GroupeScolaire
    {
        return $this->groupeScolaireRepository->find($userId);
    }
}
