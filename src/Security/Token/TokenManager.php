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
        $token = $this->tokenRepository->findOneByUser($user);

        if ($token) {
            return $token;
        }

        return $this->generateNew($user);
    }

    public function generateNew(User $user): Token
    {
        if (!$token = $this->tokenRepository->findOneByUser($user)) {
            $token = new Token($user);
            $this->tokenRepository->persist($token);
        }
        $this->setValue($token);
        $this->setExpireAt($token);
        $this->tokenRepository->flush();

        return $token;
    }

    public function setValue(Token $token): void
    {
        try {
            $token->setValue(bin2hex(random_bytes(20)));
        } catch (\Exception $e) {
            // Handle the exception appropriately
            throw new \RuntimeException('Failed to generate secure random token', 0, $e);
        }
    }

    public function setExpireAt(Token $token): void
    {
        $token->setExpireAt(new \DateTime('+90 days'));
    }

    public function loginUser(Request $request, User $user, $firewallName): void
    {
        $this->userAuthenticator->authenticateUser(
            $user,
            $this->formLoginAuthenticator,
            $request,
        );
    }

    public function isExpired(Token $token): bool
    {
        $today = new \DateTime('today');

        if( $today->format('Y-m-d') > $token->getExpireAt()->format('Y-m-d')){
            $this->tokenRepository->remove($token);
            $this->tokenRepository->flush();
            return true;
        }

        return false;
    }

    public function createForAllUsers(): void
    {
        $users = $this->userRepository->findAll();
        foreach ($users as $user) {
            $this->generateNew($user);
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
