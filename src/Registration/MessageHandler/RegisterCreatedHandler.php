<?php

namespace AcMarche\Mercredi\Registration\MessageHandler;

use AcMarche\Mercredi\Registration\EmailVerifier;
use AcMarche\Mercredi\Registration\Mailer\RegistrationMailerFactory;
use AcMarche\Mercredi\Registration\Message\RegisterCreated;
use AcMarche\Mercredi\User\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class RegisterCreatedHandler implements MessageHandlerInterface
{
    /**
     * @var FlashBagInterface
     */
    private $flashBag;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var EmailVerifier
     */
    private $emailVerifier;
    /**
     * @var RegistrationMailerFactory
     */
    private $registrationMailerFactory;

    public function __construct(
        UserRepository $userRepository,
        FlashBagInterface $flashBag,
        EmailVerifier $emailVerifier,
        RegistrationMailerFactory $registrationMailerFactory
    ) {
        $this->flashBag = $flashBag;
        $this->userRepository = $userRepository;
        $this->emailVerifier = $emailVerifier;
        $this->registrationMailerFactory = $registrationMailerFactory;
    }

    public function __invoke(RegisterCreated $registerCreated)
    {
        $userId = $registerCreated->getUserId();
        $user = $this->userRepository->find($userId);

        // generate a signed url and email it to the user
        /* $this->emailVerifier->sendEmailConfirmation(
             'app_verify_email',
             $user,
             $this->registrationMailerFactory->generateMessagToVerifyEmail($user)
         );*/

        $this->emailVerifier->sendEmailConfirmation(
            'app_verify_email',
            $user,
            $this->registrationMailerFactory->generateMessagRegisgerSuccess($user)
        );

        $this->emailVerifier->sendEmailConfirmation(
            'app_verify_email',
            $user,
            $this->registrationMailerFactory->generateMessageToAdminAccountCreated($user)
        );

        $this->flashBag->add('success', "Votre compte a bien été créé, consultez votre boite mail");
    }
}
