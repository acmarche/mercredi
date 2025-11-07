<?php

namespace AcMarche\Mercredi\Mailer\Factory;

use AcMarche\Mercredi\Mailer\InitMailerTrait;
use AcMarche\Mercredi\Mailer\TemplatedEmailFactory;
use AcMarche\Mercredi\Organisation\Traits\OrganisationPropertyInitTrait;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class ContactEmailFactory
{
    use InitMailerTrait;
    use OrganisationPropertyInitTrait;

    public function sendContactForm(string $email, string $nom, string $body): TemplatedEmail
    {
        $message = TemplatedEmailFactory::asPublicEmailJf();

        $message
            ->subject('[Mercredi] '.$nom.' vous contact via le site')
            ->from($this->getEmailSenderAddress())
            ->to($this->getEmailContact())
            ->htmlTemplate('@AcMarcheMercrediEmail/front/contact.html.twig')
            ->context(
                [
                    'body' => $body,
                    'courriel' => $email,
                ]
            );

        return $message;
    }
}
