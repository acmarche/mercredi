<?php

namespace AcMarche\Mercredi\Presence\MessageHandler;

use AcMarche\Mercredi\Presence\Message\PresenceUpdated;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler()]
final class PresenceUpdatedHandler
{
    private FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()?->getFlashBag();
    }

    public function __invoke(PresenceUpdated $presenceUpdated): void
    {
        $this->flashBag->add('success', 'La présence a bien été modifiée');
    }
}
