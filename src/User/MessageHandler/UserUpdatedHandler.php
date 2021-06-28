<?php

namespace AcMarche\Mercredi\User\MessageHandler;

use AcMarche\Mercredi\User\Message\UserUpdated;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class UserUpdatedHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    public function __invoke(UserUpdated $userUpdated): void
    {
        $this->flashBag->add('success', "L'utilisateur a bien été modifié");
    }
}
