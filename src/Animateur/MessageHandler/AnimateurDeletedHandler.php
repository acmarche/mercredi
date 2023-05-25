<?php

namespace AcMarche\Mercredi\Animateur\MessageHandler;

use AcMarche\Mercredi\Animateur\Message\AnimateurDeleted;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler()]
final class AnimateurDeletedHandler
{
    private FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()?->getFlashBag();
    }

    public function __invoke(AnimateurDeleted $animateurDeleted): void
    {
        $this->flashBag->add('success', 'L\' animateur a bien été supprimé');
    }
}
