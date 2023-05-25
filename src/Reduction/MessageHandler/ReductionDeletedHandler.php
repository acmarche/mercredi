<?php

namespace AcMarche\Mercredi\Reduction\MessageHandler;

use AcMarche\Mercredi\Reduction\Message\ReductionDeleted;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler()]
final class ReductionDeletedHandler
{
    private FlashBagInterface $flashBag;

    public function __construct(RequestStack $requestStack)
    {
        $this->flashBag = $requestStack->getSession()?->getFlashBag();
    }

    public function __invoke(ReductionDeleted $reductionDeleted): void
    {
        $this->flashBag->add('success', 'La réduction a bien été supprimée');
    }
}
