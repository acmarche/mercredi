<?php

namespace AcMarche\Mercredi\Relation\MessageHandler;

use AcMarche\Mercredi\Relation\Message\RelationUpdated;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler()]
final class RelationUpdatedHandler
{
    private FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()?->getFlashBag();
    }

    public function __invoke(RelationUpdated $relationCreated): void
    {
        $this->flashBag->add('success', 'La relation a bien été modifiée');
    }
}
