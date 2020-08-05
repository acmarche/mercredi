<?php

namespace AcMarche\Mercredi\Note\MessageHandler;

use AcMarche\Mercredi\Note\Message\NoteUpdated;
use AcMarche\Mercredi\Note\Repository\NoteRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class NoteUpdatedHandler implements MessageHandlerInterface
{
    /**
     * @var FlashBagInterface
     */
    private $flashBag;
    /**
     * @var NoteRepository
     */
    private $noteRepository;

    public function __construct(NoteRepository $noteRepository, FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
        $this->noteRepository = $noteRepository;
    }

    public function __invoke(NoteUpdated $noteUpdated): void
    {
        $this->flashBag->add('success', "L'école a bien été modifiée");
    }
}
