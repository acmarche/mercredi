<?php

namespace AcMarche\Mercredi\Scolaire\MessageHandler;

use AcMarche\Mercredi\Scolaire\Message\AnneeScolaireCreated;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class AnneeScolaireCreatedHandler implements MessageHandlerInterface
{
    private FlashBagInterface $flashBag;

    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    public function __invoke(AnneeScolaireCreated $anneeScolaireCreated): void
    {
        $this->flashBag->add('success', "L'année a bien été ajoutée");
    }
}
