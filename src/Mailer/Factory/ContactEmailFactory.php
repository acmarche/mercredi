<?php

namespace AcMarche\Mercredi\Mailer\Factory;

use AcMarche\Mercredi\Mailer\InitMailerTrait;
use AcMarche\Mercredi\Mailer\NotificationEmailJf;
use AcMarche\Mercredi\Organisation\Traits\OrganisationPropertyInitTrait;
use Symfony\Bridge\Twig\Mime\NotificationEmail;

class ContactEmailFactory
{
    use InitMailerTrait;
    use OrganisationPropertyInitTrait;

    /**
     * @return NotificationEmail
     */
    public function sendContactForm(string $from, string $nom, string $body): NotificationEmailJf
    {
        $message = NotificationEmailJf::asPublicEmailJf();

        $message
            ->subject('[Mercredi] '.$nom.' vous contact via le site')
            ->from($from)
            ->to($this->getEmailAddressOrganisationAdmin())
            ->bcc('jf@marche.be')
            ->htmlTemplate('@AcMarcheMercrediEmail/front/contact.html.twig')
            ->context(
                [
                    'body' => $body,
                ]
            );

        return $message;
    }
}
