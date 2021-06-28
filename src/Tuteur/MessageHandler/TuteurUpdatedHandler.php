<?php

namespace AcMarche\Mercredi\Tuteur\MessageHandler;

use AcMarche\Mercredi\Tuteur\Message\TuteurUpdated;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class TuteurUpdatedHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    public function __invoke(TuteurUpdated $tuteurUpdated): void
    {
        $this->flashBag->add('success', 'Le tuteur a bien été modifié');
    }
}
