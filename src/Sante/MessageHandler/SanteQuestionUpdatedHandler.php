<?php

namespace AcMarche\Mercredi\Sante\MessageHandler;

use AcMarche\Mercredi\Sante\Message\SanteQuestionUpdated;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler()]
final class SanteQuestionUpdatedHandler
{
    private FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()?->getFlashBag();
    }

    public function __invoke(SanteQuestionUpdated $santeQuestionUpdated): void
    {
        $this->flashBag->add('success', 'La question a bien été modifiée');
    }
}
