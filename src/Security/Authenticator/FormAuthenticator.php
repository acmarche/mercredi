<?php

namespace AcMarche\Mercredi\Security\Authenticator;

use AcMarche\Mercredi\User\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\PasswordUpgradeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;

class FormAuthenticator implements AuthenticatorInterface
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;
    /**
     * @var CsrfTokenManagerInterface
     */
    private $csrfTokenManager;

    public function __construct(
        UserRepository $userRepository,
        UrlGeneratorInterface $urlGenerator,
        CsrfTokenManagerInterface $csrfTokenManager
    ) {
        $this->userRepository = $userRepository;
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    public function supports(Request $request): ?bool
    {
        return 'app_login' === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    public function authenticate(Request $request): PassportInterface
    {
        // find a user based on an "email" form field
        $user = $this->userRepository->findOneByEmailOrUserName($request->get('email'));
        if (! $user) {
            throw new UsernameNotFoundException();
        }

        // return the Security passport
        return new Passport(
        // add the user we've just found
            $user,
            // add credentials from the "password" form field
            new PasswordCredentials($request->get('password')),
            [
                // and CSRF protection using a "csrf_token" field
                new CsrfTokenBadge('loginform', $request->get('csrf_token')),
                // and add support for upgrading the password hash
                new PasswordUpgradeBadge(
                    $request->get('password'),
                    $this->userRepository
                ),
            ]
        );
    }

    public function createAuthenticatedToken(PassportInterface $passport, string $firewallName): TokenInterface
    {
        $credential = $passport->getBadge(PasswordCredentials::class);

        return new UsernamePasswordToken($passport->getUser(), $credential->getPassword(), $firewallName);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new RedirectResponse($this->urlGenerator->generate('mercredi_front_home'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new RedirectResponse($this->urlGenerator->generate('mercredi_front_home'));
    }
}
