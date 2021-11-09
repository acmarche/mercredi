<?php

namespace AcMarche\Mercredi\Mailer;

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\RecipientInterface;

trait InitMailerTrait
{
    private MailerInterface $mailer;
    private NotifierInterface $notifier;

    /**
     * @required
     */
    public function setMailer(MailerInterface $mailer): void
    {
        $this->mailer = $mailer;
    }

    /**
     * @required
     */
    public function setNotifier(NotifierInterface $notifier): void
    {
        $this->notifier = $notifier;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendMail(Email $email): void
    {
        $this->mailer->send($email);
    }

    /**
     * @todo multiple recipients ??
     */
    public function sendNotifier(Notification $notification, RecipientInterface $recipients): void
    {
        $this->notifier->send($notification, $recipients);
    }
}
