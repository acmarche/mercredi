<?php


namespace AcMarche\Mercredi\Security\Authenticator;

use AcMarche\Mercredi\User\Repository\UserRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\PasswordUpgradeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/**
 * Essayer de voir les events
 * @see UserCheckerListener::postCheckCredentials
 * @see UserProviderListener::checkPassport
 * @see CheckCredentialsListener
 * bin/console debug:event-dispatcher --dispatcher=security.event_dispatcher.main
 */
class MercrediAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private UrlGeneratorInterface $urlGenerator;
    private UserRepository $userRepository;
    private ParameterBagInterface $parameterBag;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        UserRepository $userRepository,
        ParameterBagInterface $parameterBag
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->userRepository = $userRepository;
        $this->parameterBag = $parameterBag;
    }

    public function authenticate(Request $request): PassportInterface
    {
        $email = $request->request->get('username', '');
        $password = $request->request->get('password', '');
        $token = $request->request->get('_csrf_token', '');

        $request->getSession()->set(Security::LAST_USERNAME, $email);

        $badges =
            [
                new CsrfTokenBadge('authenticate', $token),
                new PasswordUpgradeBadge($password, $this->userRepository),
            ];

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($password), $badges
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('mercredi_front_profile_redirect'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
