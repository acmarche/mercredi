<?php

namespace AcMarche\Mercredi\Scolaire\MessageHandler;

use AcMarche\Mercredi\Scolaire\Message\AnneeScolaireUpdated;
use AcMarche\Mercredi\Scolaire\Repository\AnneeScolaireRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class AnneeScolaireUpdatedHandler implements MessageHandlerInterface
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

    public function __invoke(AnneeScolaireUpdated $anneeScolaireDeleted)
    {
        $this->flashBag->add('success', "L'année a bien été supprimée");
    }
}
