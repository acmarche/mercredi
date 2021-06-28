<?php

namespace AcMarche\Mercredi\Relation\MessageHandler;

use AcMarche\Mercredi\Relation\Message\RelationCreated;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class RelationCreatedHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    public function __invoke(RelationCreated $relationCreated): void
    {
        $this->flashBag->add('success', "La relation a bien été modifiée");
    }
}
