<?php


namespace AcMarche\Mercredi\Jour\MessageHandler;


use AcMarche\Mercredi\Jour\Message\JourCreated;
use AcMarche\Mercredi\Repository\JourRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class JourCreatedHandler implements MessageHandlerInterface
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

    public function __invoke(JourCreated $jourCreated)
    {
        $this->flashBag->add('success', "L'école a bien été ajoutée");
    }

}
