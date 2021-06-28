<?php

namespace AcMarche\Mercredi\Scolaire\MessageHandler;

use AcMarche\Mercredi\Scolaire\Message\GroupeScolaireDeleted;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class GroupeScolaireDeletedHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    public function __invoke(GroupeScolaireDeleted $groupeScolaireDeleted): void
    {
        $this->flashBag->add('success', 'Le groupe a bien été supprimé');
    }
}
