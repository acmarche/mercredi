<?php

namespace AcMarche\Mercredi\Document\MessageHandler;

use AcMarche\Mercredi\Document\Message\DocumentUpdated;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class DocumentUpdatedHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    public function __invoke(DocumentUpdated $documentUpdated): void
    {
        $this->flashBag->add('success', 'Le document a bien été modifié');
    }
}
