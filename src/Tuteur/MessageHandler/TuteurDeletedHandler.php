<?php

namespace AcMarche\Mercredi\Tuteur\MessageHandler;

use AcMarche\Mercredi\Tuteur\Message\TuteurDeleted;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler()]
final class TuteurDeletedHandler
{
    private FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()?->getFlashBag();
    }

    public function __invoke(TuteurDeleted $tuteurDeleted): void
    {
        $this->flashBag->add('success', 'Le tuteur a bien été supprimé');
    }
}
