<?php

namespace AcMarche\Mercredi\Accueil\MessageHandler;

use AcMarche\Mercredi\Accueil\Message\AccueilCreated;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class AccueilCreatedHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    public function __invoke(AccueilCreated $accueilCreated): void
    {
        $this->flashBag->add('success', "L'acceuil a bien été ajouté");
    }
}
