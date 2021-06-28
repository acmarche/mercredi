<?php

namespace AcMarche\Mercredi\Scolaire\MessageHandler;

use AcMarche\Mercredi\Scolaire\Message\AnneeScolaireDeleted;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class AnneeScolaireDeletedHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    public function __invoke(AnneeScolaireDeleted $anneeScolaireDeleted): void
    {
        $this->flashBag->add('success', "L'année a bien été supprimée");
    }
}
