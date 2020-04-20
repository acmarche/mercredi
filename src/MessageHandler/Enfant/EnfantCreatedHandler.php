<?php


namespace AcMarche\Mercredi\MessageHandler\Enfant;

use AcMarche\Mercredi\Message\Enfant\EnfantCreated;
use AcMarche\Mercredi\Repository\EnfantRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class EnfantCreatedHandler implements MessageHandlerInterface
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

    public function __invoke(EnfantCreated $enfantCreated)
    {
        $this->flashBag->add('success', "L'enfant a bien été ajouté");
    }

}
