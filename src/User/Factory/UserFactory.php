<?php

namespace AcMarche\Mercredi\User\Factory;

use AcMarche\Mercredi\Entity\Animateur;
use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Security\MercrediSecurity;
use AcMarche\Mercredi\Security\PasswordGenerator;
use AcMarche\Mercredi\User\Repository\UserRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFactory
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserRepository $userRepository,
        UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function getInstance(string $email = null): User
    {
        $user = new User();
        if ($email) {
            if (!$user = $this->userRepository->findOneByEmailOrUserName($email)) {
                $user = new User();
                $user->setEmail($email);
                $user->setUsername($email);
            }
        }

        $user->setEnabled(true);

        return $user;
    }

    public function newFromAnimateur(Animateur $animateur, User $user = null): User
    {
        if (!$user) {
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
        $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPlainPassword()));
        $user->addRole(MercrediSecurity::ROLE_ANIMATEUR);
        $user->addAnimateur($animateur);

        $this->userRepository->insert($user);

        return $user;
    }

    public function newFromTuteur(Tuteur $tuteur, User $user = null): User
    {
        if (!$user) {
            $user = $this->getInstance($tuteur->getEmail());
            $user->setNom($tuteur->getNom());
            $user->setPrenom($tuteur->getPreNom());
            if ($tuteur->getEmail()) {
                $user->setEmail($tuteur->getEmail());
            }
        }

        $user->setUsername($user->getEmail());
        $user->setPlainPassword(PasswordGenerator::generatePassword());
        $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPlainPassword()));

        $user->addTuteur($tuteur);
        $user->addRole(MercrediSecurity::ROLE_PARENT);
        $this->userRepository->insert($user);

        return $user;
    }
}
