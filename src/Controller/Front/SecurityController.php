<?php

namespace AcMarche\Mercredi\Controller\Front;

use AcMarche\Mercredi\Mailer\Factory\UserEmailFactory;
use AcMarche\Mercredi\Mailer\NotificationMailer;
use AcMarche\Mercredi\Security\Token\TokenManager;
use AcMarche\Mercredi\User\Repository\UserRepository;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class SecurityController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly TokenManager $tokenManager,
        private readonly UserEmailFactory $userEmailFactory,
        private readonly NotificationMailer $notificationMailer,
    ) {
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if (null !== $this->getUser()) {
            return $this->redirectToRoute('mercredi_front_profile_redirect');
        }
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render(
            '@AcMarcheMercredi/security/login.html.twig',
            [
                'last_username' => $lastUsername,
                'error' => $error,
            ],
        );
    }

    function isValidEmailStrict($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL, FILTER_FLAG_EMAIL_UNICODE) !== false;
    }

    #[Route(path: '/login/with/email', name: 'app_login_with_email')]
    public function loginWithEmail(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
        $lastUsername = $request->request->get('username');

        if (!$this->isValidEmailStrict($lastUsername)) {
            $error = new AuthenticationException('Adresse mail inconnue');
            $this->addFlash('error', $error->getMessage());

            return $this->render(
                '@AcMarcheMercredi/security/login.html.twig',
                [
                    'last_username' => $lastUsername,
                    'error' => $error,
                ],
            );
        }

        $user = $this->userRepository->loadUserByIdentifier($lastUsername);

        if (!$user) {
            $error = new AuthenticationException('Adresse mail inconnue');
            $this->addFlash('error', $error->getMessage());

            return $this->render(
                '@AcMarcheMercredi/security/login.html.twig',
                [
                    'last_username' => $lastUsername,
                    'error' => $error,
                ],
            );
        }

        $error = $authenticationUtils->getLastAuthenticationError();

        $tokenUrl = $this->tokenManager->getLinkToConnect($user);

        try {
            $message = $this->userEmailFactory->messageSendAutoLogin($user, $tokenUrl);
            $this->notificationMailer->sendAsEmailNotification($message, $user->getEmail());
            $this->addFlash('success', 'Un lien pour vous connecter vous a été envoyé sur votre boite mail');

            return $this->redirectToRoute('mercredi_front_home');
        } catch (\Exception $exception) {
            $error = new AuthenticationException('Le mail n\'a pas pu être envoyé '.$error->getMessage());
        }

        return $this->render(
            '@AcMarcheMercredi/security/login.html.twig',
            [
                'last_username' => $lastUsername,
                'error' => $error,
            ],
        );
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new LogicException(
            'This method can be blank - it will be intercepted by the logout key on your firewall.',
        );
    }

}
