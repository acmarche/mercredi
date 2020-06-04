<?php

namespace AcMarche\Mercredi\Tuteur\MessageHandler;

use AcMarche\Mercredi\Tuteur\Message\TuteurCreated;
use AcMarche\Mercredi\Tuteur\Repository\TuteurRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class TuteurCreatedHandler implements MessageHandlerInterface
{
    /**
     * @var FlashBagInterface
     */
    private $flashBag;
    /**
     * @var TuteurRepository
     */
    private $tuteurRepository;

    public function __construct(TuteurRepository $tuteurRepository, FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
        $this->tuteurRepository = $tuteurRepository;
    }

    public function __invoke(TuteurCreated $tuteurCreated)
    {
        $this->flashBag->add('success', 'Le tuteur a bien été ajouté');
    }
}
