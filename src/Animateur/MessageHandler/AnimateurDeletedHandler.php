<?php

namespace AcMarche\Mercredi\Animateur\MessageHandler;

use AcMarche\Mercredi\Animateur\Message\AnimateurDeleted;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class AnimateurDeletedHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    public function __invoke(AnimateurDeleted $animateurDeleted): void
    {
        $this->flashBag->add('success', 'L\' animateur a bien été supprimé');
    }
}
