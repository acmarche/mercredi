<?php

namespace AcMarche\Mercredi\Ecole\MessageHandler;

use AcMarche\Mercredi\Ecole\Message\EcoleCreated;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class EcoleCreatedHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    public function __invoke(EcoleCreated $ecoleCreated): void
    {
        $this->flashBag->add('success', "L'école a bien été ajoutée");
    }
}
