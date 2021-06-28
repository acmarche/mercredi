<?php

namespace AcMarche\Mercredi\Reduction\MessageHandler;

use AcMarche\Mercredi\Reduction\Message\ReductionDeleted;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class ReductionDeletedHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    public function __invoke(ReductionDeleted $reductionDeleted): void
    {
        $this->flashBag->add('success', 'La réduction a bien été supprimée');
    }
}
