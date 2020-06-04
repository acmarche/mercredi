<?php

namespace AcMarche\Mercredi\Tuteur\MessageHandler;

use AcMarche\Mercredi\Tuteur\Message\TuteurUpdated;
use AcMarche\Mercredi\Tuteur\Repository\TuteurRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class TuteurUpdatedHandler implements MessageHandlerInterface
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

    public function __invoke(TuteurUpdated $tuteurUpdated)
    {
        $this->flashBag->add('success', 'Le tuteur a bien été modifié');
    }
}
