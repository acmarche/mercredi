<?php

namespace AcMarche\Mercredi\Page\MessageHandler;

use AcMarche\Mercredi\Page\Message\PageDeleted;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler()]
final class PageDeletedHandler
{
    private FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()?->getFlashBag();
    }

    public function __invoke(PageDeleted $pageDeleted): void
    {
        $this->flashBag->add('success', 'La page a bien été supprimée');
    }
}
