<?php

namespace AcMarche\Mercredi\Accueil\MessageHandler;

use AcMarche\Mercredi\Accueil\Message\AccueilCreated;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler()]
final class AccueilCreatedHandler
{
    private FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()?->getFlashBag();
    }

    public function __invoke(AccueilCreated $accueilCreated): void
    {
        $this->flashBag->add('success', "L'acceuil a bien été ajouté");
    }
}
