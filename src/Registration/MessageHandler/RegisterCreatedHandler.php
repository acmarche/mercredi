<?php

namespace AcMarche\Mercredi\Registration\MessageHandler;

use AcMarche\Mercredi\Mailer\Factory\RegistrationMailerFactory;
use AcMarche\Mercredi\Mailer\NotificationMailer;
use AcMarche\Mercredi\Parameter\Option;
use AcMarche\Mercredi\Registration\Message\RegisterCreated;
use AcMarche\Mercredi\User\Repository\UserRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
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
    private ParameterBagInterface $parameterBag;

    public function __construct(
        UserRepository $userRepository,
        FlashBagInterface $flashBag,
        RegistrationMailerFactory $registrationMailerFactory,
        VerifyEmailHelperInterface $verifyEmailHelper,
        NotificationMailer $notificationMailer,
        ParameterBagInterface $parameterBag
    ) {
        $this->flashBag = $flashBag;
        $this->userRepository = $userRepository;
        $this->registrationMailerFactory = $registrationMailerFactory;
        $this->verifyEmailHelper = $verifyEmailHelper;
        $this->notificationMailer = $notificationMailer;
        $this->parameterBag = $parameterBag;
    }

    public function isOpen(): bool
    {
        $register = (bool)$this->parameterBag->get(Option::REGISTER);

        return $register == true;
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

        $email = $this->registrationMailerFactory->generateMessagRegisgerSuccess(
            $user,
            $verifyEmailSignatureComponents
        );
        $this->notificationMailer->sendAsEmailNotification($email, $user->getEmail());

        $email = $this->registrationMailerFactory->generateMessageToAdminAccountCreated($user);
        $this->notificationMailer->sendAsEmailNotification($email, $user->getEmail());

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
