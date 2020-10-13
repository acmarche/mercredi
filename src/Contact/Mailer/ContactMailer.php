<?php

namespace AcMarche\Mercredi\Contact\Mailer;

use AcMarche\Mercredi\Mailer\InitMailerTrait;
use AcMarche\Mercredi\Organisation\Traits\OrganisationPropertyInitTrait;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

final class ContactMailer
{
    use InitMailerTrait;
    use OrganisationPropertyInitTrait;

    public function __invoke(): void
    {
        $this->organisation = $this->organisationRepository->getOrganisation();
    }

    /**
     * @param string $from
     * @param string $nom
     * @param string $body
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
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
