<?php

namespace AcMarche\Mercredi\Mailer\Factory;

use AcMarche\Mercredi\Mailer\InitMailerTrait;
use AcMarche\Mercredi\Organisation\Traits\OrganisationPropertyInitTrait;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Notifier\Notification\Notification;

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
    public function sendContactForm(string $from, string $nom, string $body): NotificationEmail
    {
        $to = $this->organisationRepository->getOrganisation() !== null ? $this->organisation->getEmail(
        ) : 'nomail@domain.be';
        $message = NotificationEmail::asPublicEmail();
        $message->importance(Notification::IMPORTANCE_HIGH);

        $message
            ->subject('[Mercredi] '.$nom.' vous contact via le site')
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
