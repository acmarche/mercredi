<?php

namespace AcMarche\Mercredi\Fixture;

use AcMarche\Mercredi\Entity\Security\User;
use Fidry\AliceDataFixtures\ProcessorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserProcessor implements ProcessorInterface
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordEncoder
    ) {
    }

    public function preProcess(string $fixtureId, $user): void
    {
        if (! $user instanceof User) {
            return;
        }

        $user->setPassword($this->userPasswordEncoder->hashPassword($user, $user->getPassword()));
    }

    public function postProcess(string $fixtureId, $user): void
    {
        // do nothing
    }
}
