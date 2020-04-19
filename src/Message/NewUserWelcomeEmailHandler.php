<?php


namespace AcMarche\Mercredi\Message;


use AcMarche\Mercredi\Repository\UserRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class NewUserWelcomeEmailHandler implements MessageHandlerInterface
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function __invoke(NewUserWelcomeEmail $welcomeEmail)
    {
        $user = $this->userRepository->find($welcomeEmail->getEnfantId());
        // ... send an email!
    }
}
