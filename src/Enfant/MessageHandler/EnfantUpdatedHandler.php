<?php

namespace AcMarche\Mercredi\Enfant\MessageHandler;

use AcMarche\Mercredi\Enfant\Message\EnfantUpdated;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler()]
final class EnfantUpdatedHandler
{
    private FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()?->getFlashBag();
    }

    public function __invoke(EnfantUpdated $enfantUpdated): void
    {
        $this->flashBag->add('success', "L'enfant a bien été modifié");
    }
}
