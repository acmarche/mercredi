<?php

namespace AcMarche\Mercredi\Presence\MessageHandler;

use AcMarche\Mercredi\Presence\Message\PresenceUpdated;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class PresenceUpdatedHandler implements MessageHandlerInterface
{
    /**
     * @var FlashBagInterface
     */
    private $flashBag;
    /**
     * @var PresenceRepository
     */
    private $presenceRepository;

    public function __construct(PresenceRepository $presenceRepository, FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
        $this->presenceRepository = $presenceRepository;
    }

    public function __invoke(PresenceUpdated $presenceUpdated): void
    {
        $this->flashBag->add('success', 'La présence a bien été modifiée');
    }
}
