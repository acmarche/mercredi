<?php


namespace AcMarche\Mercredi\Sante\MessageHandler;

use AcMarche\Mercredi\Sante\Message\SanteQuestionUpdated;
use AcMarche\Mercredi\Sante\Repository\SanteQuestionRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class SanteQuestionUpdatedHandler implements MessageHandlerInterface
{
    /**
     * @var FlashBagInterface
     */
    private $flashBag;
    /**
     * @var SanteQuestionRepository
     */
    private $santeQuestionRepository;

    public function __construct(SanteQuestionRepository $santeQuestionRepository, FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
        $this->santeQuestionRepository = $santeQuestionRepository;
    }

    public function __invoke(SanteQuestionUpdated $santeQuestionUpdated)
    {
        $this->flashBag->add('success', "La question a bien été modifiée");
    }

}
