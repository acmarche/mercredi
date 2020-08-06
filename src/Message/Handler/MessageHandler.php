<?php

namespace AcMarche\Mercredi\Message\Handler;

use AcMarche\Mercredi\Entity\Message;
use AcMarche\Mercredi\Mailer\InitMailerTrait;
use AcMarche\Mercredi\Message\Factory\EmailFactory;
use AcMarche\Mercredi\Message\Repository\MessageRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

final class MessageHandler
{
    use InitMailerTrait;

    /**
     * @var MessageRepository
     */
    private $messageRepository;
    /**
     * @var EmailFactory
     */
    private $emailFactory;
    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    public function __construct(
        MessageRepository $messageRepository,
        EmailFactory $emailFactory,
        FlashBagInterface $flashBag
    ) {
        $this->messageRepository = $messageRepository;
        $this->emailFactory = $emailFactory;
        $this->flashBag = $flashBag;
    }

    public function handle(Message $message): void
    {
        $templatedEmail = $this->emailFactory->create($message);

        foreach ($message->getDestinataires() as $addressEmail) {
            $templatedEmail->to($addressEmail);

            try {
                $this->sendMail($templatedEmail);
            } catch (TransportExceptionInterface $e) {
                $this->flashBag->add('danger', 'Erreur pour l\'email '.$addressEmail.': '.$e->getMessage());
            }
        }

        $this->messageRepository->persist($message);
        $this->messageRepository->flush();
    }
}
