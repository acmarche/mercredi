<?php

namespace AcMarche\Mercredi\Facture\MessageHandler;

use AcMarche\Mercredi\Facture\Message\FactureUpdated;
use AcMarche\Mercredi\Facture\Repository\FactureRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class FactureUpdatedHandler implements MessageHandlerInterface
{
    /**
     * @var FlashBagInterface
     */
    private $flashBag;
    /**
     * @var FactureRepository
     */
    private $factureRepository;

    public function __construct(FactureRepository $factureRepository, FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
        $this->factureRepository = $factureRepository;
    }

    public function __invoke(FactureUpdated $factureUpdated): void
    {
        $this->flashBag->add('success', 'La facture a bien été modifiée');
    }
}
