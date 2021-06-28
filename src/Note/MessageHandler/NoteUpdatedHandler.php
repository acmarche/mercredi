<?php

namespace AcMarche\Mercredi\Note\MessageHandler;

use AcMarche\Mercredi\Note\Message\NoteUpdated;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class NoteUpdatedHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    public function __invoke(NoteUpdated $noteUpdated): void
    {
        $this->flashBag->add('success', "La note a bien été modifiée");
    }
}
