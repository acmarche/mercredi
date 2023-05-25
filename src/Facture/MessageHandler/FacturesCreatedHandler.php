<?php

namespace AcMarche\Mercredi\Facture\MessageHandler;

use AcMarche\Mercredi\Facture\Message\FacturesCreated;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler()]
final class FacturesCreatedHandler
{
    private FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()?->getFlashBag();
    }

    public function __invoke(FacturesCreated $facturesCreated): void
    {
        $count = \count($facturesCreated->getFactureIds());
        $this->flashBag->add('success', 'Les '.$count.' factures ont bien été crées');
    }
}
