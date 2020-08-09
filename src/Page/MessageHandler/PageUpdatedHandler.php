<?php

namespace AcMarche\Mercredi\Page\MessageHandler;

use AcMarche\Mercredi\Page\Message\PageUpdated;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class PageUpdatedHandler implements MessageHandlerInterface
{
    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    public function __invoke(PageUpdated $pageUpdated): void
    {
        $this->flashBag->add('success', 'La page a bien été modifiée');
    }
}
