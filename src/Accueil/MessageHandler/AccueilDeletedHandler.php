<?php

namespace AcMarche\Mercredi\Accueil\MessageHandler;

use AcMarche\Mercredi\Accueil\Message\AccueilDeleted;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class AccueilDeletedHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    public function __invoke(AccueilDeleted $accueilDeleted): void
    {
        $this->flashBag->add('success', "L'acceuil a bien été supprimé");
    }
}
