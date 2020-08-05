<?php

namespace AcMarche\Mercredi\Animateur\MessageHandler;

use AcMarche\Mercredi\Animateur\Message\AnimateurCreated;
use AcMarche\Mercredi\Animateur\Repository\AnimateurRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class AnimateurCreatedHandler implements MessageHandlerInterface
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

    public function __invoke(AnimateurCreated $animateurCreated): void
    {
        $this->flashBag->add('success', 'L\' animateur a bien été ajouté');
    }
}
