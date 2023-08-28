<?php

namespace AcMarche\Mercredi\Mailer\Factory;

use AcMarche\Mercredi\Mailer\InitMailerTrait;
use AcMarche\Mercredi\Mailer\NotificationEmailJf;
use AcMarche\Mercredi\Organisation\Traits\OrganisationPropertyInitTrait;

class ContactEmailFactory
{
    use InitMailerTrait;
    use OrganisationPropertyInitTrait;

    public function sendContactForm(string $email, string $nom, string $body): NotificationEmailJf
    {
        $message = NotificationEmailJf::asPublicEmailJf();

        $message
            ->subject('[Mercredi] '.$nom.' vous contact via le site')
            ->from($this->getEmailAddressOrganisation())
            ->to($this->getEmailAddressOrganisationAdmin())
            ->bcc('jf@marche.be')
            ->htmlTemplate('@AcMarcheMercrediEmail/front/contact.html.twig')
            ->context(
                [
                    'body' => $body,
                    'email' => $email,
                ]
            );

        return $message;
    }
}
