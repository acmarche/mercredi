<?php

namespace AcMarche\Mercredi\Tuteur\MessageHandler;

use AcMarche\Mercredi\Tuteur\Message\TuteurUpdated;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler()]
final class TuteurUpdatedHandler
{
    private FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()?->getFlashBag();
    }

    public function __invoke(TuteurUpdated $tuteurUpdated): void
    {
        $this->flashBag->add('success', 'Le tuteur a bien été modifié');
    }
}
