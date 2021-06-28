<?php

namespace AcMarche\Mercredi\Plaine\MessageHandler;

use AcMarche\Mercredi\Plaine\Message\PlaineCreated;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class PlaineCreatedHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    public function __invoke(PlaineCreated $plaineCreated): void
    {
        $this->flashBag->add('success', 'La plaine a bien été ajoutée');
    }
}
