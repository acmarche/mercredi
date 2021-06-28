<?php

namespace AcMarche\Mercredi\Ecole\MessageHandler;

use AcMarche\Mercredi\Ecole\Message\EcoleUpdated;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class EcoleUpdatedHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    public function __invoke(EcoleUpdated $ecoleUpdated): void
    {
        $this->flashBag->add('success', "L'école a bien été modifiée");
    }
}
