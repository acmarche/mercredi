<?php

namespace AcMarche\Mercredi\Sante\MessageHandler;

use AcMarche\Mercredi\Sante\Message\SanteFicheUpdated;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class SanteFicheUpdatedHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    public function __invoke(SanteFicheUpdated $santeFicheUpdated): void
    {
        $this->flashBag->add('success', 'Le formulaire santé a bien été enregistré');
    }
}
