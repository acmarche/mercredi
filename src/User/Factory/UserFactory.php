<?php

namespace AcMarche\Mercredi\User\Factory;

use AcMarche\Mercredi\Entity\Animateur;
use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Security\PasswordGenerator;
use AcMarche\Mercredi\Security\Role\MercrediSecurityRole;
use AcMarche\Mercredi\User\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserFactory
{
    private UserRepository $userRepository;
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(
        UserRepository $userRepository,
        UserPasswordHasherInterface $userPasswordHasher
    ) {
        $this->userRepository = $userRepository;
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function getInstance(?string $email = null): ?User
    {
        $user = new User();
        if ($email && !$user = $this->userRepository->findOneByEmailOrUserName($email)) {
            $user = new User();
            $user->setEmail($email);
            $user->setUsername($email);
        }

        $user->setEnabled(true);

        return $user;
    }

    public function newFromAnimateur(Animateur $animateur, ?User $user = null): ?User
    {
        if (!$user instanceof User) {
            $user = $this->getInstance($animateur->getEmail());
            $user->setNom($animateur->getNom());
            $user->setPrenom($animateur->getPreNom());
            if ($animateur->getEmail()) {
                $user->setEmail($animateur->getEmail());
            }
        }

        $user->setUsername($user->getEmail());
        $user->setUsername($user->getEmail());
        $user->setPlainPassword(PasswordGenerator::generatePassword());
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $user->getPlainPassword()));
        $user->addRole(MercrediSecurityRole::ROLE_ANIMATEUR);

        $this->userRepository->persist($user);
        $this->userRepository->flush();

        return $user;
    }

    public function newFromTuteur(Tuteur $tuteur, ?User $user = null): ?User
    {
        if (!$user instanceof User) {
            $user = $this->getInstance($tuteur->getEmail());
            $user->setNom($tuteur->getNom());
            $user->setPrenom($tuteur->getPreNom());
            if ($tuteur->getEmail()) {
                $user->setEmail($tuteur->getEmail());
            }
        }

        $user->setUsername($user->getEmail());
        $user->setPlainPassword(PasswordGenerator::generatePassword());
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $user->getPlainPassword()));

        $user->addTuteur($tuteur);
        $user->addRole(MercrediSecurityRole::ROLE_PARENT);
        $this->userRepository->persist($user);
        $this->userRepository->flush();

        return $user;
    }
}
