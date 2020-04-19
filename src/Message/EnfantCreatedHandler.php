<?php


namespace AcMarche\Mercredi\Message;


use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class EnfantCreatedHandler implements MessageHandlerInterface
{
    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    public function __construct(FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
    }

    public function __invoke(EnfantCreated $enfantCreatedEvent)
    {

        $this->flashBag->add('success', 'Enfant créé');
    }
}
