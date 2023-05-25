<?php

namespace AcMarche\Mercredi\Registration\MessageHandler;

use AcMarche\Mercredi\Mailer\Factory\RegistrationMailerFactory;
use AcMarche\Mercredi\Mailer\NotificationMailer;
use AcMarche\Mercredi\Parameter\Option;
use AcMarche\Mercredi\Registration\Message\RegisterCreated;
use AcMarche\Mercredi\User\Repository\UserRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Security\Core\User\UserInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

#[AsMessageHandler()]
final class RegisterCreatedHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private RequestStack $requestStack,
        private RegistrationMailerFactory $registrationMailerFactory,
        private VerifyEmailHelperInterface $verifyEmailHelper,
        private NotificationMailer $notificationMailer,
        private ParameterBagInterface $parameterBag
    ) {

    }

    public function __invoke(RegisterCreated $registerCreated): void
    {
        $userId = $registerCreated->getUserId();
        $user = $this->userRepository->find($userId);

        // generate a signed url and email it to the user
        /* $this->emailVerifier->sendEmailConfirmation(
             'app_verify_email',
             $user,
             $this->registrationMailerFactory->generateMessagToVerifyEmail($user)
         );*/

        $verifyEmailSignatureComponents = $this->verifyEmailHelper->generateSignature(
            'app_verify_email',
            $user->getId(),
            $user->getEmail()
        );

        $message = $this->registrationMailerFactory->generateMessagRegisgerSuccess(
            $user,
            $verifyEmailSignatureComponents
        );
        $this->notificationMailer->sendAsEmailNotification($message, $user->getEmail());

        $message = $this->registrationMailerFactory->generateMessageToAdminAccountCreated($user);
        $this->notificationMailer->sendAsEmailNotification($message, $user->getEmail());
        $flashBag = $this->requestStack->getSession()?->getFlashBag();
        $flashBag->add('success', 'Votre compte a bien été créé, consultez votre boite mail');
    }

    public function isOpen(): bool
    {
        return (int)$this->parameterBag->get(Option::REGISTER) > 0;
    }

    /**
     * @throws VerifyEmailExceptionInterface
     */
    public function handleEmailConfirmation(Request $request, UserInterface $user): void
    {
        $this->verifyEmailHelper->validateEmailConfirmation($request->getUri(), $user->getId(), $user->getEmail());

        $user->setIsVerified(true);

        $this->userRepository->persist($user);
        $this->userRepository->flush();
    }
}
