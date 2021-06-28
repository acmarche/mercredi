<?php

namespace AcMarche\Mercredi\Ecole\MessageHandler;

use AcMarche\Mercredi\Ecole\Message\EcoleDeleted;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class EcoleDeletedHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    public function __invoke(EcoleDeleted $ecoleDeleted): void
    {
        $this->flashBag->add('success', "L'école a bien été supprimée");
    }
}
