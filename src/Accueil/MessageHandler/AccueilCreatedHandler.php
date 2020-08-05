<?php

namespace AcMarche\Mercredi\Accueil\MessageHandler;

use AcMarche\Mercredi\Accueil\Message\AccueilCreated;
use AcMarche\Mercredi\Accueil\Repository\AccueilRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class AccueilCreatedHandler implements MessageHandlerInterface
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

    public function __invoke(AccueilCreated $accueilCreated): void
    {
        $this->flashBag->add('success', "L'acceuil a bien été ajouté");
    }
}
