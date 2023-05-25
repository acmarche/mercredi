<?php

namespace AcMarche\Mercredi\Accueil\MessageHandler;

use AcMarche\Mercredi\Accueil\Message\AccueilUpdated;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler()]
final class AccueilUpdatedHandler
{
    private FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()?->getFlashBag();
    }

    public function __invoke(AccueilUpdated $accueilUpdated): void
    {
        $this->flashBag->add('success', "L'accueil a bien été modifié");
    }
}
