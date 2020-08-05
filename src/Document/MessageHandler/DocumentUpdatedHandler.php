<?php

namespace AcMarche\Mercredi\Document\MessageHandler;

use AcMarche\Mercredi\Document\Message\DocumentUpdated;
use AcMarche\Mercredi\Document\Repository\DocumentRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class DocumentUpdatedHandler implements MessageHandlerInterface
{
    /**
     * @var FlashBagInterface
     */
    private $flashBag;
    /**
     * @var DocumentRepository
     */
    private $documentRepository;

    public function __construct(DocumentRepository $documentRepository, FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
        $this->documentRepository = $documentRepository;
    }

    public function __invoke(DocumentUpdated $documentUpdated): void
    {
        $this->flashBag->add('success', 'Le document a bien été modifié');
    }
}
