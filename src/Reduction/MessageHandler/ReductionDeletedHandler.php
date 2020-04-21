<?php


namespace AcMarche\Mercredi\Reduction\MessageHandler;

use AcMarche\Mercredi\Reduction\Message\ReductionDeleted;
use AcMarche\Mercredi\Repository\ReductionRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ReductionDeletedHandler implements MessageHandlerInterface
{
    /**
     * @var FlashBagInterface
     */
    private $flashBag;
    /**
     * @var ReductionRepository
     */
    private $reductionRepository;

    public function __construct(ReductionRepository $reductionRepository, FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
        $this->reductionRepository = $reductionRepository;
    }

    public function __invoke(ReductionDeleted $reductionDeleted)
    {
        $this->flashBag->add('success', "La réduction a bien été suppriméé");
    }

}
