<?php

namespace AcMarche\Mercredi\Document\MessageHandler;

use AcMarche\Mercredi\Document\Message\DocumentUpdated;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler()]
final class DocumentUpdatedHandler
{
    private FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()?->getFlashBag();
    }

    public function __invoke(DocumentUpdated $documentUpdated): void
    {
        $this->flashBag->add('success', 'Le document a bien été modifié');
    }
}
