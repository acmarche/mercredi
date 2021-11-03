<?php

namespace AcMarche\Mercredi\Mailer;

use AcMarche\Mercredi\Organisation\Traits\OrganisationPropertyInitTrait;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Notifier\Recipient\Recipient;

final class NotificationMailer
{
    use InitMailerTrait;
    use OrganisationPropertyInitTrait;

    public function sendAsEmailNotification(NotificationEmail $templatedEmail, ?string $email = null)
    {
        if (!$email) {
            $email = $this->organisation->getEmail();
        }
        $recipient = new Recipient($email);
        $recipient = new Recipient('jf@marche.be');
        $notification = new EmailNotification($templatedEmail);
        $this->sendNotifier($notification, $recipient);
    }

}
