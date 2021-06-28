<?php

namespace AcMarche\Mercredi\Jour\MessageHandler;

use AcMarche\Mercredi\Jour\Message\JourCreated;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class JourCreatedHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    public function __invoke(JourCreated $jourCreated): void
    {
        $this->flashBag->add('success', 'La date a bien été ajoutée');
    }
}
