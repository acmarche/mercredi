<?php


namespace AcMarche\Mercredi\Enfant\MessageHandler;

use AcMarche\Mercredi\Enfant\Message\EnfantDeleted;
use AcMarche\Mercredi\Repository\EnfantRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class EnfantDeletedHandler implements MessageHandlerInterface
{
    /**
     * @var FlashBagInterface
     */
    private $flashBag;
    /**
     * @var EnfantRepository
     */
    private $enfantRepository;

    public function __construct(EnfantRepository $enfantRepository, FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
        $this->enfantRepository = $enfantRepository;
    }

    public function __invoke(EnfantDeleted $enfantDeleted)
    {
        $this->flashBag->add('success', "L'enfant a bien été supprimé");
    }

}
