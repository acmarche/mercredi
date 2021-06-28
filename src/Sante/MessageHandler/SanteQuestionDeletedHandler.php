<?php

namespace AcMarche\Mercredi\Sante\MessageHandler;

use AcMarche\Mercredi\Sante\Message\SanteQuestionDeleted;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class SanteQuestionDeletedHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    public function __invoke(SanteQuestionDeleted $santeQuestionDeleted): void
    {
        $this->flashBag->add('success', 'La question a bien été supprimée');
    }
}
