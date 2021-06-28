<?php

namespace AcMarche\Mercredi\Relation\MessageHandler;

use AcMarche\Mercredi\Relation\Message\RelationDeleted;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class RelationDeletedHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    public function __invoke(RelationDeleted $relationDeleted): void
    {
        $this->flashBag->add('success', 'La relation a bien été supprimée');
    }
}
