<?php

namespace AcMarche\Mercredi\Page\MessageHandler;

use AcMarche\Mercredi\Page\Message\PageUpdated;
use AcMarche\Mercredi\Page\Repository\PageRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class PageUpdatedHandler implements MessageHandlerInterface
{
    /**
     * @var FlashBagInterface
     */
    private $flashBag;
    /**
     * @var PageRepository
     */
    private $pageRepository;

    public function __construct(PageRepository $pageRepository, FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
        $this->pageRepository = $pageRepository;
    }

    public function __invoke(PageUpdated $pageUpdated): void
    {
        $this->flashBag->add('success', 'La page a bien été modifiée');
    }
}
