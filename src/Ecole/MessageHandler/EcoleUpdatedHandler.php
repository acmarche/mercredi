<?php


namespace AcMarche\Mercredi\Ecole\MessageHandler;

use AcMarche\Mercredi\Ecole\Message\EcoleUpdated;
use AcMarche\Mercredi\Ecole\Repository\EcoleRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class EcoleUpdatedHandler implements MessageHandlerInterface
{
    /**
     * @var FlashBagInterface
     */
    private $flashBag;
    /**
     * @var EcoleRepository
     */
    private $ecoleRepository;

    public function __construct(EcoleRepository $ecoleRepository, FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
        $this->ecoleRepository = $ecoleRepository;
    }

    public function __invoke(EcoleUpdated $ecoleUpdated)
    {
        $this->flashBag->add('success', "L'école a bien été modifiée");
    }

}
