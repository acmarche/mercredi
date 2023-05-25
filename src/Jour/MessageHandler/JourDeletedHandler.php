<?php

namespace AcMarche\Mercredi\Jour\MessageHandler;

use AcMarche\Mercredi\Jour\Message\JourDeleted;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler()]
final class JourDeletedHandler
{
    private FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()?->getFlashBag();
    }

    public function __invoke(JourDeleted $jourDeleted): void
    {
        $this->flashBag->add('success', 'La date a bien été supprimée');
    }
}
