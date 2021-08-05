<?php

namespace AcMarche\Mercredi\Mailer;

use AcMarche\Mercredi\Mailer\EmailNotification;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\NoRecipient;
use Symfony\Component\Notifier\Recipient\Recipient;

/**
 * je sais pas changer priorite
 */
$notification = (new Notification('New Invoice', ['email']))
    ->content('You got a new invoice for 15 EUR.')
    ->importance(Notification::IMPORTANCE_HIGH)
    ->emoji('🤩');

// The receiver of the Notification
$recipient = new Recipient(
    'jf@marche.be',
    '+32476662615'
);

$norecipient = new NoRecipient();
// Send the notification to the recipient
// $this->notifier->send($notification, $recipient);

$emailNotification = new EmailNotification($tuteur, 'coucou');
$this->notifier->send($notification, $recipient);

