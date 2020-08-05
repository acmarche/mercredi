<?php

namespace AcMarche\Mercredi\Reduction\MessageHandler;

use AcMarche\Mercredi\Reduction\Message\ReductionCreated;
use AcMarche\Mercredi\Reduction\Repository\ReductionRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ReductionCreatedHandler implements MessageHandlerInterface
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

    public function __invoke(ReductionCreated $reductionCreated): void
    {
        $this->flashBag->add('success', 'La réduction a bien été ajoutée');
    }
}
