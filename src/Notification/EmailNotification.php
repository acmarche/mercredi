<?php

namespace AcMarche\Mercredi\Notification;

use AcMarche\Mercredi\Entity\Tuteur;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Notification\EmailNotificationInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;
use Symfony\Component\Notifier\Recipient\RecipientInterface;

class EmailNotification extends Notification implements EmailNotificationInterface
{
    private Tuteur $tuteur;

    public function __construct(Tuteur $tuteur, string $subject = '', array $channels = [])
    {
        parent::__construct($subject, $channels);
        $this->tuteur = $tuteur;
    }

    public function asEmailMessage(EmailRecipientInterface $recipient, string $transport = null): ?EmailMessage
    {
        // ici je ne sais pas retirer le LOW dans le sujet
        $message = EmailMessage::fromNotification($this, $recipient);

        //Symfony\Bridge\Twig\Mime\NotificationEmail //extends TemplatedEmail
        $message = NotificationEmail::asPublicEmail();
        //$message->theme('zurb_2');
        $message->markdown('Ce **mot** en gras.');

        $message
            ->to($recipient->getEmail())
            ->subject($this->getSubject())
          //  ->content($this->getContent() ?: $this->getSubject())
            //->htmlTemplate('@AcMarcheMercrediAdmin/message/x.html.twig')
            ->context(['tuteur' => $this->tuteur, 'importance' => null]);

        $message->markAsPublic();//remove importance, footer

        return new EmailMessage($message);
    }

    public function getChannels(RecipientInterface $recipient): array
    {
        $this->importance(Notification::IMPORTANCE_LOW);

        return ['email'];
    }
}
