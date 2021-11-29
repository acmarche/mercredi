<?php

namespace AcMarche\Mercredi\Mailer\Factory;

use AcMarche\Mercredi\Mailer\InitMailerTrait;
use AcMarche\Mercredi\Mailer\NotificationEmailJf;
use AcMarche\Mercredi\Organisation\Traits\OrganisationPropertyInitTrait;

class ContactEmailFactory
{
    use InitMailerTrait;
    use OrganisationPropertyInitTrait;

    /**
     * @param string $from
     * @param string $nom
     * @param string $body
     * @return \Symfony\Bridge\Twig\Mime\NotificationEmail
     */
    public function sendContactForm(string $from, string $nom, string $body): \Symfony\Bridge\Twig\Mime\NotificationEmail
    {
        $to = $this->getEmailAddressOrganisation();
        $message = NotificationEmailJf::asPublicEmailJf();

        $message
            ->subject('[Mercredi] ' . $nom . ' vous contact via le site')
            ->from($from)
            ->to($to)
            ->htmlTemplate('@AcMarcheMercrediEmail/front/contact.html.twig')
            ->context(
                [
                    'body' => $body,
                ]
            );

        return $message;
    }
}
