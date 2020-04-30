<?php


namespace AcMarche\Mercredi\Jour\MessageHandler;

use AcMarche\Mercredi\Jour\Message\JourDeleted;
use AcMarche\Mercredi\Jour\Repository\JourRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class JourDeletedHandler implements MessageHandlerInterface
{
    /**
     * @var FlashBagInterface
     */
    private $flashBag;
    /**
     * @var JourRepository
     */
    private $jourRepository;

    public function __construct(JourRepository $jourRepository, FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
        $this->jourRepository = $jourRepository;
    }

    public function __invoke(JourDeleted $jourDeleted)
    {
        $this->flashBag->add('success', "L'école a bien été supprimée");
    }

}
