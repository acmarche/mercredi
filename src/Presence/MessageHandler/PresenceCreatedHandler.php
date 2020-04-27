<?php


namespace AcMarche\Mercredi\Presence\MessageHandler;


use AcMarche\Mercredi\Presence\Message\PresenceCreated;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class PresenceCreatedHandler implements MessageHandlerInterface
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

    public function __invoke(PresenceCreated $presenceCreated)
    {
        $this->flashBag->add('success', "L'école a bien été ajoutée");
    }

}
