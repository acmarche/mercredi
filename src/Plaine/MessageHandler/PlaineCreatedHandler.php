<?php

namespace AcMarche\Mercredi\Plaine\MessageHandler;

use AcMarche\Mercredi\Plaine\Message\PlaineCreated;
use AcMarche\Mercredi\Plaine\Repository\PlaineRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class PlaineCreatedHandler implements MessageHandlerInterface
{
    /**
     * @var FlashBagInterface
     */
    private $flashBag;
    /**
     * @var PlaineRepository
     */
    private $plaineRepository;

    public function __construct(PlaineRepository $plaineRepository, FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
        $this->plaineRepository = $plaineRepository;
    }

    public function __invoke(PlaineCreated $plaineCreated)
    {
        $this->flashBag->add('success', "La plaine a bien été ajoutée");
    }
}
