<?php

namespace AcMarche\Mercredi\Security\Token;

use AcMarche\Mercredi\Entity\Security\Token;
use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\User\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\FormLoginAuthenticator;

class TokenManager
{
    public function __construct(
        private readonly UserAuthenticatorInterface $userAuthenticator,
        private readonly FormLoginAuthenticator $formLoginAuthenticator,
        private readonly RouterInterface $router,
        private readonly TokenRepository $tokenRepository,
        private readonly UserRepository $userRepository,
    ) {
    }

    public function getInstance(User $user): Token
    {
        if (!$token = $this->tokenRepository->findOneByUser($user) === null) {
            $token = new Token();
            $token->setUser($user);
            $this->tokenRepository->persist($token);
        }

        return $token;
    }

    public function loginUser(Request $request, User $user, $firewallName): void
    {
        $this->userAuthenticator->authenticateUser(
            $user,
            $this->formLoginAuthenticator,
            $request,
        );
    }

    public function generate(User $user, ?\DateTime $expireAt = null): Token
    {
        $token = $this->getInstance($user);
        try {
            $token->setValue(bin2hex(random_bytes(20)));
        } catch (\Exception) {
        }

        if (!$expireAt instanceof \DateTime) {
            $expireAt = new \DateTime('+90 day');
        }

        $token->setExpireAt($expireAt);

        $this->tokenRepository->flush();

        return $token;
    }

    public function isExpired(Token $token): bool
    {
        $today = new \DateTime('today');

        return $today > $token->getExpireAt();
    }

    public function createForAllUsers(): void
    {
        $users = $this->userRepository->findAll();
        foreach ($users as $user) {
            $this->generate($user);
        }

        $this->userRepository->flush();
    }

    public function getLinkToConnect(User $user): ?string
    {
        $token = $this->getInstance($user);

        return $this->router->generate(
            'mercredi_security_autologin',
            ['value' => $token->getValue()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }
}
