<?php

namespace AcMarche\Mercredi\Sante\MessageHandler;

use AcMarche\Mercredi\Sante\Message\SanteQuestionDeleted;
use AcMarche\Mercredi\Sante\Repository\SanteQuestionRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class SanteQuestionDeletedHandler implements MessageHandlerInterface
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

    public function __invoke(SanteQuestionDeleted $santeQuestionDeleted): void
    {
        $this->flashBag->add('success', 'La question a bien été supprimée');
    }
}
