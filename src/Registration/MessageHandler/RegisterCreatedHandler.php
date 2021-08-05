<?php

namespace AcMarche\Mercredi\Registration\MessageHandler;

use AcMarche\Mercredi\Mailer\Factory\RegistrationMailerFactory;
use AcMarche\Mercredi\Mailer\NotificationMailer;
use AcMarche\Mercredi\Registration\Message\RegisterCreated;
use AcMarche\Mercredi\User\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Security\Core\User\UserInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

final class RegisterCreatedHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;
    private UserRepository $userRepository;
    private RegistrationMailerFactory $registrationMailerFactory;
    private VerifyEmailHelperInterface $verifyEmailHelper;
    private NotificationMailer $notificationMailer;

    public function __construct(
        UserRepository $userRepository,
        FlashBagInterface $flashBag,
        RegistrationMailerFactory $registrationMailerFactory,
        VerifyEmailHelperInterface $verifyEmailHelper,
        NotificationMailer $notificationMailer
    ) {
        $this->flashBag = $flashBag;
        $this->userRepository = $userRepository;
        $this->registrationMailerFactory = $registrationMailerFactory;
        $this->verifyEmailHelper = $verifyEmailHelper;
        $this->notificationMailer = $notificationMailer;
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

        $recipient = new Recipient($user->getEmail());

        $email = $this->registrationMailerFactory->generateMessagRegisgerSuccess(
            $user,
            $verifyEmailSignatureComponents
        );
        $this->notificationMailer->sendAsEmailNotification($email, $recipient);

        $email = $this->registrationMailerFactory->generateMessageToAdminAccountCreated($user);
        $recipient = new Recipient($user->getEmail());
        $this->notificationMailer->sendAsEmailNotification($email, $recipient);

        $this->flashBag->add('success', 'Votre compte a bien été créé, consultez votre boite mail');
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
