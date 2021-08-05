<?php

namespace AcMarche\Mercredi\Mailer;

use AcMarche\Mercredi\Organisation\Traits\OrganisationPropertyInitTrait;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Notifier\Recipient\RecipientInterface;

final class NotificationMailer
{
    use InitMailerTrait;
    use OrganisationPropertyInitTrait;

    public function sendAsEmailNotification(NotificationEmail $templatedEmail, RecipientInterface $recipients)
    {
        $notification = new EmailNotification($templatedEmail);
        $this->sendNotifier($notification, $recipients);
    }

}
