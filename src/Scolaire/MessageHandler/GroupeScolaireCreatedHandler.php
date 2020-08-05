<?php

namespace AcMarche\Mercredi\Scolaire\MessageHandler;

use AcMarche\Mercredi\Scolaire\Message\GroupeScolaireCreated;
use AcMarche\Mercredi\Scolaire\Repository\GroupeScolaireRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class GroupeScolaireCreatedHandler implements MessageHandlerInterface
{
    /**
     * @var FlashBagInterface
     */
    private $flashBag;
    /**
     * @var GroupeScolaireRepository
     */
    private $groupeScolaireRepository;

    public function __construct(GroupeScolaireRepository $groupeScolaireRepository, FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
        $this->groupeScolaireRepository = $groupeScolaireRepository;
    }

    public function __invoke(GroupeScolaireCreated $groupeScolaireCreated): void
    {
        $this->flashBag->add('success', 'Le groupe a bien été ajouté');
    }
}
