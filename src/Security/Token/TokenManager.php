<?php

namespace AcMarche\Mercredi\Security\Token;

use AcMarche\Mercredi\Entity\Security\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\FormLoginAuthenticator;

class TokenManager
{
    public function __construct(
        private UserAuthenticatorInterface $userAuthenticator,
        private FormLoginAuthenticator $formLoginAuthenticator,
        private RouterInterface $router
    ) {
    }


    public function createForAllUsers(): void
    {
        $users = $this->userRepository->findAll();
        foreach ($users as $user) {
            $user->setUuid($user->generateUuid());
        }
        $this->userRepository->flush();
    }

    public function loginUser(Request $request, User $user, $firewallName): void
    {
        $this->userAuthenticator->authenticateUser(
            $user,
            $this->formLoginAuthenticator,
            $request,
        );
    }

    public function getLinkToConnect(User $user): ?string
    {
        if (!$user->getUuid()) {
            return null;
        }

        return $this->router->generate(
            'mercredi_security_autologin',
            ['uuid' => $user->getUuid()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }
}
