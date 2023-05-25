<?php

namespace AcMarche\Mercredi\Enfant\MessageHandler;

use AcMarche\Mercredi\Enfant\Message\EnfantCreated;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler()]
final class EnfantCreatedHandler
{
    private FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()?->getFlashBag();
    }

    public function __invoke(EnfantCreated $enfantCreated): void
    {
        $this->flashBag->add('success', "L'enfant a bien été ajouté");
    }
}
