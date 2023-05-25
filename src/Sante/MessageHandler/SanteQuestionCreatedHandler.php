<?php

namespace AcMarche\Mercredi\Sante\MessageHandler;

use AcMarche\Mercredi\Sante\Message\SanteQuestionCreated;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler()]
final class SanteQuestionCreatedHandler
{
    private FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()?->getFlashBag();
    }

    public function __invoke(SanteQuestionCreated $santeQuestionCreated): void
    {
        $this->flashBag->add('success', 'La question a bien été ajoutée');
    }
}
