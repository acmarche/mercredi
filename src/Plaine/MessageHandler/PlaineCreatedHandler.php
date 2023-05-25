<?php

namespace AcMarche\Mercredi\Plaine\MessageHandler;

use AcMarche\Mercredi\Plaine\Message\PlaineCreated;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler()]
final class PlaineCreatedHandler
{
    private FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()?->getFlashBag();
    }

    public function __invoke(PlaineCreated $plaineCreated): void
    {
        $this->flashBag->add('success', 'La plaine a bien été ajoutée');
    }
}
