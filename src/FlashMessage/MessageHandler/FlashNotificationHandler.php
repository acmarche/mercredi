<?php


namespace AcMarche\Mercredi\FlashMessage\MessageHandler;

use AcMarche\Mercredi\FlashMessage\Message\FlashNotification;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class FlashNotificationHandler implements MessageHandlerInterface
{
    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    public function __invoke(FlashNotification $flashNotification)
    {
        $this->flashBag->add($flashNotification->getType(), $flashNotification->getMessage());
    }
}
