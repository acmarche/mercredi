<?php

namespace AcMarche\Mercredi\User\MessageHandler;

use AcMarche\Mercredi\User\Message\UserDeleted;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler()]
final class UserDeletedHandler
{
    private FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()?->getFlashBag();
    }

    public function __invoke(UserDeleted $userDeleted): void
    {
        $this->flashBag->add('success', "L'utilisateur a bien été supprimé");
    }
}
