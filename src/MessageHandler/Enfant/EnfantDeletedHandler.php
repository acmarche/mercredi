<?php


namespace AcMarche\Mercredi\MessageHandler\Enfant;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Message\Enfant\EnfantCreated;
use AcMarche\Mercredi\Message\Enfant\EnfantDeleted;
use AcMarche\Mercredi\Repository\EnfantRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

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

    public function __invoke(EnfantDeleted $enfantCreated)
    {
        $this->flashBag->add('success', "L'enfant a bien été supprimé");
    }

}
