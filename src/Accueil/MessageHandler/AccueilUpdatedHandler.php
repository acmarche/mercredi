<?php

namespace AcMarche\Mercredi\Accueil\MessageHandler;

use AcMarche\Mercredi\Accueil\Message\AccueilUpdated;
use AcMarche\Mercredi\Accueil\Repository\AccueilRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class AccueilUpdatedHandler implements MessageHandlerInterface
{
    /**
     * @var FlashBagInterface
     */
    private $flashBag;
    /**
     * @var AccueilRepository
     */
    private $accueilRepository;

    public function __construct(AccueilRepository $accueilRepository, FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
        $this->accueilRepository = $accueilRepository;
    }

    public function __invoke(AccueilUpdated $accueilUpdated)
    {
        $this->flashBag->add('success', "L'accueil a bien été modifié");
    }
}
