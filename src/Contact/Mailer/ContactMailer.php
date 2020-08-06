<?php

namespace AcMarche\Mercredi\Contact\Mailer;

use AcMarche\Mercredi\Mailer\InitMailerTrait;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

final class ContactMailer
{

    use InitMailerTrait;

    public function __invoke()
    {
        $this->organisation = $this->organisationRepository->getOrganisation();
    }

    public function sendContactForm(string $from, string $nom, string $body): void
    {
        $to = $this->organisationRepository->getOrganisation() ? $this->organisation->getEmail() : 'nomail@domain.be';

        $templatedEmail = (new TemplatedEmail())
            ->subject('[Mercredi] '.$nom.' vous contact via le site')
            ->from($from)
            ->to($to)
            ->text($body);

        $this->sendMail($templatedEmail);
    }
}
