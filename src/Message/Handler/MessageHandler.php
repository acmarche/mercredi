<?php

namespace AcMarche\Mercredi\Message\Handler;

use AcMarche\Mercredi\Entity\Message;
use AcMarche\Mercredi\Message\Factory\EmailFactory;
use AcMarche\Mercredi\Message\Repository\MessageRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class MessageHandler
{
    /**
     * @var MessageRepository
     */
    private $messageRepository;
    /**
     * @var EmailFactory
     */
    private $emailFactory;
    /**
     * @var MailerInterface
     */
    private $mailer;
    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    public function __construct(
        MessageRepository $messageRepository,
        EmailFactory $emailFactory,
        MailerInterface $mailer,
        FlashBagInterface $flashBag
    ) {
        $this->messageRepository = $messageRepository;
        $this->emailFactory = $emailFactory;
        $this->mailer = $mailer;
        $this->flashBag = $flashBag;
    }

    public function handle(Message $message)
    {
        $email = $this->emailFactory->create($message);

        foreach ($message->getDestinataires() as $addressEmail) {
            $email->to($addressEmail);
            try {
                $this->mailer->send($email);
            } catch (TransportExceptionInterface $e) {
                $this->flashBag->add('danger', 'Erreur pour l\'email '.$addressEmail.': '.$e->getMessage());
            }
        }

        $this->messageRepository->persist($message);
        $this->messageRepository->flush();
    }
}
