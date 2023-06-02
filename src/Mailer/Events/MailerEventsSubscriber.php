<?php

namespace AcMarche\Mercredi\Mailer\Events;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Event\FailedMessageEvent;
use Symfony\Component\Mailer\Event\SentMessageEvent;

class MailerEventsSubscriber implements EventSubscriberInterface
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public static function getSubscribedEvents()
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
        $this->logger->error("Mail sent from ".$from->toString()." to ".$to);
    }

    public function onMessageFailure(FailedMessageEvent $event): void
    {
        $message = $event->getMessage();
        $this->logger->error("Mail error ".$message->toString());
    }
}