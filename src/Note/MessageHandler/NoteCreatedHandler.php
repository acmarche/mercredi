<?php

namespace AcMarche\Mercredi\Note\MessageHandler;

use AcMarche\Mercredi\Note\Message\NoteCreated;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler()]
final class NoteCreatedHandler
{
    private FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()?->getFlashBag();
    }

    public function __invoke(NoteCreated $noteCreated): void
    {
        $this->flashBag->add('success', 'La note a bien été ajoutée');
    }
}
