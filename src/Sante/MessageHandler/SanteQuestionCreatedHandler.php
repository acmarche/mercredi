<?php

namespace AcMarche\Mercredi\Sante\MessageHandler;

use AcMarche\Mercredi\Sante\Message\SanteQuestionCreated;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class SanteQuestionCreatedHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    public function __invoke(SanteQuestionCreated $santeQuestionCreated): void
    {
        $this->flashBag->add('success', 'La question a bien été ajoutée');
    }
}
