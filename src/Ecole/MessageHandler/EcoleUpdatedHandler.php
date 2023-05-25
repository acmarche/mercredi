<?php

namespace AcMarche\Mercredi\Ecole\MessageHandler;

use AcMarche\Mercredi\Ecole\Message\EcoleUpdated;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler()]
final class EcoleUpdatedHandler
{
    private FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()?->getFlashBag();
    }

    public function __invoke(EcoleUpdated $ecoleUpdated): void
    {
        $this->flashBag->add('success', "L'école a bien été modifiée");
    }
}
