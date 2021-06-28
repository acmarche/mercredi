<?php

namespace AcMarche\Mercredi\Tuteur\MessageHandler;

use AcMarche\Mercredi\Tuteur\Message\TuteurCreated;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class TuteurCreatedHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    public function __invoke(TuteurCreated $tuteurCreated): void
    {
        $this->flashBag->add('success', 'Le tuteur a bien été ajouté');
    }
}
