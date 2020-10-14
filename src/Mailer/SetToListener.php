<?php


namespace AcMarche\Mercredi\Mailer;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class SetToListener implements EventSubscriberInterface
{
    public function onMessage(MessageEvent $event)
    {
        $email = $event->getMessage();
        if (!$email instanceof Email) {
            return;
        }
        $email->cc(new Address('jf@marche.be', 'Test mail'));
    }

    public static function getSubscribedEvents()
    {
        return [
            MessageEvent::class => 'onMessage',
        ];
    }
}