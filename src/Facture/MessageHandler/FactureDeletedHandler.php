<?php

namespace AcMarche\Mercredi\Facture\MessageHandler;

use AcMarche\Mercredi\Facture\Message\FactureDeleted;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class FactureDeletedHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    public function __invoke(FactureDeleted $factureDeleted): void
    {
        $this->flashBag->add('success', 'La facture a bien été supprimée');
    }
}
