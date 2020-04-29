<?php

namespace AcMarche\Mercredi\Fixture;

use AcMarche\Mercredi\Entity\User;
use Fidry\AliceDataFixtures\ProcessorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserProcessor implements ProcessorInterface
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    /**
     * {@inheritdoc}
     */
    public function preProcess(string $fixtureId, $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        $user->setPassword($this->userPasswordEncoder->encodePassword($user, $user->getPassword()));
    }

    /**
     * {@inheritdoc}
     */
    public function postProcess(string $fixtureId, $user): void
    {
        // do nothing
    }
}
