<?php

namespace AcMarche\Mercredi\Scolaire\MessageHandler;

use AcMarche\Mercredi\Scolaire\Message\AnneeScolaireCreated;
use AcMarche\Mercredi\Scolaire\Repository\AnneeScolaireRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class AnneeScolaireCreatedHandler implements MessageHandlerInterface
{
    /**
     * @var FlashBagInterface
     */
    private $flashBag;
    /**
     * @var AnneeScolaireRepository
     */
    private $anneeScolaireRepository;

    public function __construct(AnneeScolaireRepository $anneeScolaireRepository, FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
        $this->anneeScolaireRepository = $anneeScolaireRepository;
    }

    public function __invoke(AnneeScolaireCreated $groupeScolaireCreated)
    {
        $this->flashBag->add('success', "L'année a bien été ajoutée");
    }
}
