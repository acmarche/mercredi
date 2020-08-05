<?php

namespace AcMarche\Mercredi\Animateur\MessageHandler;

use AcMarche\Mercredi\Animateur\Message\AnimateurDeleted;
use AcMarche\Mercredi\Animateur\Repository\AnimateurRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class AnimateurDeletedHandler implements MessageHandlerInterface
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

    public function __invoke(AnimateurDeleted $animateurDeleted): void
    {
        $this->flashBag->add('success', 'L\' animateur a bien été supprimé');
    }
}
