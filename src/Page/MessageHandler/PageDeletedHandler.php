<?php

namespace AcMarche\Mercredi\Page\MessageHandler;

use AcMarche\Mercredi\Page\Message\PageDeleted;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class PageDeletedHandler implements MessageHandlerInterface
{
    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    public function __invoke(PageDeleted $pageDeleted): void
    {
        $this->flashBag->add('success', 'La page a bien été supprimée');
    }
}
