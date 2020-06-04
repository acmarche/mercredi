<?php

namespace AcMarche\Mercredi\Sante\MessageHandler;

use AcMarche\Mercredi\Sante\Message\SanteFicheDeleted;
use AcMarche\Mercredi\Sante\Repository\SanteFicheRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class SanteFicheDeletedHandler implements MessageHandlerInterface
{
    /**
     * @var FlashBagInterface
     */
    private $flashBag;
    /**
     * @var SanteFicheRepository
     */
    private $santeFicheRepository;

    public function __construct(SanteFicheRepository $santeFicheRepository, FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
        $this->santeFicheRepository = $santeFicheRepository;
    }

    public function __invoke(SanteFicheDeleted $santeFicheDeleted)
    {
        $this->flashBag->add('success', 'La question a bien été supprimée');
    }
}
