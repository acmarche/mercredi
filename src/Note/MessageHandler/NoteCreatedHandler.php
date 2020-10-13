<?php

namespace AcMarche\Mercredi\Note\MessageHandler;

use AcMarche\Mercredi\Note\Message\NoteCreated;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class NoteCreatedHandler implements MessageHandlerInterface
{
    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    public function __invoke(NoteCreated $noteCreated): void
    {
        $this->flashBag->add('success', "La note a bien été ajoutée");
    }
}
