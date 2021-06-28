<?php

namespace AcMarche\Mercredi\Enfant\MessageHandler;

use AcMarche\Mercredi\Enfant\Message\EnfantDeleted;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class EnfantDeletedHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    public function __invoke(EnfantDeleted $enfantDeleted): void
    {
        $this->flashBag->add('success', "L'enfant a bien été supprimé");
    }
}
