<?php


namespace AcMarche\Mercredi\Note\MessageHandler;

use AcMarche\Mercredi\Note\Message\NoteDeleted;
use AcMarche\Mercredi\Note\Repository\NoteRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class NoteDeletedHandler implements MessageHandlerInterface
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

    public function __invoke(NoteDeleted $noteDeleted)
    {
        $this->flashBag->add('success', "L'école a bien été suppriméé");
    }

}
