<?php

namespace AcMarche\Mercredi\Facture\MessageHandler;

use AcMarche\Mercredi\Facture\Message\FactureDeleted;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler()]
final class FactureDeletedHandler
{
    private FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()?->getFlashBag();
    }

    public function __invoke(FactureDeleted $factureDeleted): void
    {
        $this->flashBag->add('success', 'La facture a bien été supprimée');
    }
}
