<?php

namespace AcMarche\Mercredi\Fixture;

use AcMarche\Mercredi\Entity\Security\User;
use Fidry\AliceDataFixtures\ProcessorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class UserProcessor implements ProcessorInterface
{
    private UserPasswordEncoderInterface $userPasswordEncoder;

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function preProcess(string $fixtureId, $user): void
    {
        if (! $user instanceof User) {
            return;
        }

        $user->setPassword($this->userPasswordEncoder->encodePassword($user, $user->getPassword()));
    }

    public function postProcess(string $fixtureId, $user): void
    {
        // do nothing
    }
}
