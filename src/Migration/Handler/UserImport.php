<?php

namespace AcMarche\Mercredi\Migration\Handler;

use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Migration\MercrediPdo;
use AcMarche\Mercredi\User\Repository\UserRepository;
use Symfony\Component\Console\Style\SymfonyStyle;

class UserImport
{
    private UserRepository $userRepository;
    private MercrediPdo $pdo;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function import(SymfonyStyle $io): void
    {
        $this->pdo = new MercrediPdo();
        $enfants = $this->pdo->getAll('users');
        foreach ($enfants as $data) {
            $io->writeln($data->nom);
            $user = new User();
            $user->setIdOld($data->id);
            $user->setNom($data->nom);
            $user->setPrenom($data->prenom);
            $user->setEmail($data->email);
            $user->setUsername($data->username);
            $user->setEnabled(true);
            $user->setRoles($this->getRoles($data->id));
            $user->setPassword($data->password);
            if ($data->salt) {
                $user->setSalt($data->salt);
            }
            $user->setTelephone($data->telephone);
            $this->userRepository->persist($user);
        }
        $this->userRepository->flush();
    }

    private function getRoles(int $userId): array
    {
        $roles = [];
        $rows = $this->pdo->getAllWhere('fos_user_group', 'user_id = '.$userId, false);
        foreach ($rows as $row) {
            $groupe = $this->pdo->getAllWhere('fos_group', 'id = '.$row->group_id, true);
            $roles[] = 'ROLE_'.$groupe->name;
        }

        return $roles;
    }
}
