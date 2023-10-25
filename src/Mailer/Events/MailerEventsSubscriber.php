<?php

namespace AcMarche\Mercredi\Mailer\Events;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Event\FailedMessageEvent;
use Symfony\Component\Mailer\Event\SentMessageEvent;

class MailerEventsSubscriber implements EventSubscriberInterface
{
    public function __construct(private LoggerInterface $mercredilogger)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SentMessageEvent::class => 'onMessageSent',
            FailedMessageEvent::class => 'onMessageFailure',
        ];

    }

    public function onMessageSent(SentMessageEvent $event): void
    {
        $message = $event->getMessage();
        $recipients = $message->getEnvelope()->getRecipients();
        $to = '';
        foreach ($recipients as $recipient) {
            $to .= $recipient->toString();
        }
        $from = $message->getEnvelope()->getSender();
        $this->mercredilogger->info("[MERCREDI] Mail sent from ".$from->toString()." to ".$to);
    }

    public function onMessageFailure(FailedMessageEvent $event): void
    {
        $message = $event->getMessage();
        $this->mercredilogger->error("[MERCREDI] Mail error ".$message->toString());
    }
}