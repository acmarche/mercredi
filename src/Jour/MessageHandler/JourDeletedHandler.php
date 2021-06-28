<?php

namespace AcMarche\Mercredi\Jour\MessageHandler;

use AcMarche\Mercredi\Jour\Message\JourDeleted;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class JourDeletedHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    public function __invoke(JourDeleted $jourDeleted): void
    {
        $this->flashBag->add('success', 'La date a bien été supprimée');
    }
}
