<?php

namespace AcMarche\Mercredi\User\MessageHandler;

use AcMarche\Mercredi\User\Message\UserCreated;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class UserCreatedHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    public function __invoke(UserCreated $userCreated): void
    {
        $this->flashBag->add('success', "L'utilisateur a bien été ajouté");
    }
}
