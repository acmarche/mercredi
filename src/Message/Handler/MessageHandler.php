<?php

namespace AcMarche\Mercredi\Message\Handler;

use AcMarche\Mercredi\Entity\Message;
use AcMarche\Mercredi\Entity\Plaine\Plaine;
use AcMarche\Mercredi\Mailer\InitMailerTrait;
use AcMarche\Mercredi\Mailer\NotificationMailer;
use AcMarche\Mercredi\Message\Factory\EmailFactory;
use AcMarche\Mercredi\Message\Repository\MessageRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

final class MessageHandler
{
    use InitMailerTrait;
    private FlashBagInterface $flashBag;

    public function __construct(
        private MessageRepository $messageRepository,
        private EmailFactory $emailFactory,
        private NotificationMailer $notificationMailer,
        RequestStack $requestStack
    ) {
        $this->flashBag = $requestStack->getSession()?->getFlashBag();
    }

    public function handle(Message $message): void
    {
        $templatedEmail = $this->emailFactory->create($message);

        foreach ($message->getDestinataires() as $addressEmail) {
            $templatedEmail->to($addressEmail);
            $this->notificationMailer->sendAsEmailNotification($templatedEmail, $addressEmail);
        }

        $this->messageRepository->persist($message);
        $this->messageRepository->flush();
    }

    public function handleFromPlaine(Plaine $plaine, Message $message, bool $attachCourrier): void
    {
        $templatedEmail = $this->emailFactory->createForPlaine($plaine, $message, $attachCourrier);

        foreach ($message->getDestinataires() as $addressEmail) {
            $templatedEmail->to($addressEmail);
            $this->notificationMailer->sendAsEmailNotification($templatedEmail);
        }

        $this->messageRepository->persist($message);
        $this->messageRepository->flush();
    }
}
