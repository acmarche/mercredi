<?php

namespace AcMarche\Mercredi\Relation\MessageHandler;

use AcMarche\Mercredi\Relation\Message\RelationDeleted;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler()]
final class RelationDeletedHandler
{
    private FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()?->getFlashBag();
    }

    public function __invoke(RelationDeleted $relationDeleted): void
    {
        $this->flashBag->add('success', 'La relation a bien été supprimée');
    }
}
