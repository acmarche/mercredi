<?php

namespace AcMarche\Mercredi\Animateur\MessageHandler;

use AcMarche\Mercredi\Animateur\Message\AnimateurUpdated;
use AcMarche\Mercredi\Animateur\Repository\AnimateurRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class AnimateurUpdatedHandler implements MessageHandlerInterface
{
    /**
     * @var FlashBagInterface
     */
    private $flashBag;
    /**
     * @var AnimateurRepository
     */
    private $animateurRepository;

    public function __construct(AnimateurRepository $animateurRepository, FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
        $this->animateurRepository = $animateurRepository;
    }

    public function __invoke(AnimateurUpdated $animateurUpdated): void
    {
        $this->flashBag->add('success', 'L\' animateur a bien été modifié');
    }
}
