<?php

namespace AcMarche\Mercredi\Facture\MessageHandler;

use AcMarche\Mercredi\Facture\Message\FactureUpdated;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class FactureUpdatedHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    public function __invoke(FactureUpdated $factureUpdated): void
    {
        $this->flashBag->add('success', 'La facture a bien été modifiée');
    }
}
